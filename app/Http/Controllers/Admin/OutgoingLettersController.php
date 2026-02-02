<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyOutgoingLetterRequest;
use App\Http\Requests\Admin\StoreOutgoingLetterRequest;
use App\Http\Requests\Admin\UpdateOutgoingLetterRequest;
use App\Models\IncomingLetter;
use App\Models\LettersOrganization;
use App\Models\OutgoingLetter;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class OutgoingLettersController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('outgoing_letter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = OutgoingLetter::where('is_archived', false)->with(['recevier', 'incoming_letter', 'created_by'])->select(sprintf('%s.*', (new OutgoingLetter)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'outgoing_letter_show';
                $editGate      = 'outgoing_letter_edit';
                $deleteGate    = 'outgoing_letter_delete';
                $crudRoutePart = 'outgoing-letters';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('letter_number', function ($row) {
                return $row->letter_number ? $row->letter_number : '';
            });

            $table->addColumn('recevier_name', function ($row) {
                return $row->recevier ? $row->recevier->name : '';
            });

            $table->editColumn('subject', function ($row) {
                return $row->subject ? $row->subject : '';
            });
            $table->editColumn('priority', function ($row) {
                return $row->priority ? OutgoingLetter::PRIORITY_SELECT[$row->priority] : '';
            });
            $table->editColumn('outgoing_type', function ($row) {
                return $row->outgoing_type ? OutgoingLetter::OUTGOING_TYPE_SELECT[$row->outgoing_type] : '';
            });
            $table->editColumn('attachments', function ($row) {
                if (! $row->attachments) {
                    return '';
                }
                $links = [];
                foreach ($row->attachments as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });
            $table->addColumn('incoming_letter_letter_number', function ($row) {
                return $row->incoming_letter ? $row->incoming_letter->letter_number : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'recevier', 'attachments', 'incoming_letter', 'created_by']);

            return $table->make(true);
        }

        return view('admin.outgoingLetters.index');
    }

    public function create()
    {
        abort_if(Gate::denies('outgoing_letter_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $receviers = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $letter_organizations = LettersOrganization::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $incoming_letters = IncomingLetter::pluck('letter_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.outgoingLetters.create', compact('incoming_letters', 'receviers', 'letter_organizations'));
    }

    public function store(StoreOutgoingLetterRequest $request)
    {
        $validatedRequest = $request->validated();
        $validatedRequest['created_by_id'] = auth()->id();
        $outgoingLetter = OutgoingLetter::create($validatedRequest);

        foreach ($request->input('attachments', []) as $file) {
            $outgoingLetter->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $outgoingLetter->id]);
        }

        return redirect()->route('admin.outgoing-letters.index');
    }

    public function edit(OutgoingLetter $outgoingLetter)
    {
        abort_if(Gate::denies('outgoing_letter_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $receviers = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $incoming_letters = IncomingLetter::pluck('letter_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        $letter_organizations = LettersOrganization::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $outgoingLetter->load('recevier', 'incoming_letter', 'created_by');

        return view('admin.outgoingLetters.edit', compact('incoming_letters', 'outgoingLetter', 'receviers', 'letter_organizations'));
    }

    public function update(UpdateOutgoingLetterRequest $request, OutgoingLetter $outgoingLetter)
    {
        $outgoingLetter->update($request->all());

        if (count($outgoingLetter->attachments) > 0) {
            foreach ($outgoingLetter->attachments as $media) {
                if (! in_array($media->file_name, $request->input('attachments', []))) {
                    $media->delete();
                }
            }
        }
        $media = $outgoingLetter->attachments->pluck('file_name')->toArray();
        foreach ($request->input('attachments', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $outgoingLetter->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
            }
        }

        return redirect()->route('admin.outgoing-letters.index');
    }

    public function show(OutgoingLetter $outgoingLetter)
    {
        abort_if(Gate::denies('outgoing_letter_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $outgoingLetter->load('recevier', 'incoming_letter', 'created_by');

        return view('admin.outgoingLetters.show', compact('outgoingLetter'));
    }

    public function destroy(OutgoingLetter $outgoingLetter)
    {
        abort_if(Gate::denies('outgoing_letter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $outgoingLetter->delete();

        return back();
    }

    public function massDestroy(MassDestroyOutgoingLetterRequest $request)
    {
        $outgoingLetters = OutgoingLetter::find(request('ids'));

        foreach ($outgoingLetters as $outgoingLetter) {
            $outgoingLetter->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('outgoing_letter_create') && Gate::denies('outgoing_letter_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new OutgoingLetter();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
