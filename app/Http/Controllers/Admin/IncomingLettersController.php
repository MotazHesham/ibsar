<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyIncomingLetterRequest;
use App\Http\Requests\Admin\StoreIncomingLetterRequest;
use App\Http\Requests\Admin\UpdateIncomingLetterRequest;
use App\Models\IncomingLetter;
use App\Models\LettersOrganization;
use App\Models\OutgoingLetter;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class IncomingLettersController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('incoming_letter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = IncomingLetter::where('is_archived', false)->with(['recevier', 'letter_organization', 'outgoing_letter', 'created_by'])->select(sprintf('%s.*', (new IncomingLetter)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'incoming_letter_show';
                $editGate      = 'incoming_letter_edit';
                $deleteGate    = 'incoming_letter_delete';
                $crudRoutePart = 'incoming-letters';
                $prependButtons = [
                    [
                        'gate' => 'archive_create',
                        'title' => trans('global.archive'),
                        'url' => '#',
                        'color' => 'success',
                        'icon' => 'ri-inbox-archive-line',
                        'attributes' => 'onclick="addToArchive(' . $row->id . ',\'IncomingLetter\')"',
                    ]
                ];
                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                    'prependButtons'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('letter_number', function ($row) {
                return $row->letter_number ? $row->letter_number : '';
            });
            $table->editColumn('external_number', function ($row) {
                return $row->external_number ? $row->external_number : '';
            });

            $table->addColumn('recevier_name', function ($row) {
                return $row->recevier ? $row->recevier->name : '';
            });

            $table->editColumn('subject', function ($row) {
                return $row->subject ? $row->subject : '';
            });
            $table->editColumn('priority', function ($row) {
                return $row->priority ? IncomingLetter::PRIORITY_SELECT[$row->priority] : '';
            });
            $table->editColumn('incoming_type', function ($row) {
                return $row->incoming_type ? IncomingLetter::INCOMING_TYPE_SELECT[$row->incoming_type] : '';
            });
            $table->addColumn('letter_organization_name', function ($row) {
                return $row->letter_organization ? $row->letter_organization->name : '';
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
            $table->addColumn('outgoing_letter_letter_number', function ($row) {
                return $row->outgoing_letter ? $row->outgoing_letter->letter_number : '';
            });

            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'recevier', 'letter_organization', 'attachments', 'outgoing_letter', 'created_by']);

            return $table->make(true);
        }

        return view('admin.incomingLetters.index');
    }

    public function create()
    {
        abort_if(Gate::denies('incoming_letter_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $receviers = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $letter_organizations = LettersOrganization::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $outgoing_letters = OutgoingLetter::pluck('letter_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.incomingLetters.create', compact('letter_organizations', 'outgoing_letters', 'receviers'));
    }

    public function store(StoreIncomingLetterRequest $request)
    {
        $validatedRequest = $request->validated();
        $validatedRequest['created_by_id'] = auth()->id();
        $incomingLetter = IncomingLetter::create($validatedRequest);

        foreach ($request->input('attachments', []) as $file) {
            $incomingLetter->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $incomingLetter->id]);
        }

        return redirect()->route('admin.incoming-letters.index');
    }

    public function edit(IncomingLetter $incomingLetter)
    {
        abort_if(Gate::denies('incoming_letter_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $receviers = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $letter_organizations = LettersOrganization::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $outgoing_letters = OutgoingLetter::pluck('letter_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        $incomingLetter->load('recevier', 'letter_organization', 'outgoing_letter', 'created_by');

        return view('admin.incomingLetters.edit', compact('incomingLetter', 'letter_organizations', 'outgoing_letters', 'receviers'));
    }

    public function update(UpdateIncomingLetterRequest $request, IncomingLetter $incomingLetter)
    {
        $incomingLetter->update($request->all());

        if (count($incomingLetter->attachments) > 0) {
            foreach ($incomingLetter->attachments as $media) {
                if (! in_array($media->file_name, $request->input('attachments', []))) {
                    $media->delete();
                }
            }
        }
        $media = $incomingLetter->attachments->pluck('file_name')->toArray();
        foreach ($request->input('attachments', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $incomingLetter->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
            }
        }

        return redirect()->route('admin.incoming-letters.index');
    }

    public function show(IncomingLetter $incomingLetter)
    {
        abort_if(Gate::denies('incoming_letter_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $incomingLetter->load('recevier', 'letter_organization', 'outgoing_letter', 'created_by');

        return view('admin.incomingLetters.show', compact('incomingLetter'));
    }

    public function destroy(IncomingLetter $incomingLetter)
    {
        abort_if(Gate::denies('incoming_letter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $incomingLetter->delete();

        return back();
    }

    public function massDestroy(MassDestroyIncomingLetterRequest $request)
    {
        $incomingLetters = IncomingLetter::find(request('ids'));

        foreach ($incomingLetters as $incomingLetter) {
            $incomingLetter->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('incoming_letter_create') && Gate::denies('incoming_letter_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new IncomingLetter();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
