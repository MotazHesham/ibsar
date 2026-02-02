<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyBeneficiaryFileRequest;
use App\Http\Requests\Admin\StoreBeneficiaryFileRequest;
use App\Http\Requests\Admin\UpdateBeneficiaryFileRequest;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFile;
use App\Models\RequiredDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BeneficiaryFilesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('beneficiary_file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BeneficiaryFile::with(['beneficiary', 'required_document'])->select(sprintf('%s.*', (new BeneficiaryFile)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'beneficiary_file_show';
                $editGate      = 'beneficiary_file_edit';
                $deleteGate    = 'beneficiary_file_delete';
                $crudRoutePart = 'beneficiary-files';

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
            $table->addColumn('beneficiary_dob', function ($row) {
                return $row->beneficiary ? $row->beneficiary->dob : '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('file', function ($row) {
                return $row->file ? '<a href="' . $row->file->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>' : '';
            });
            $table->addColumn('required_document_name', function ($row) {
                return $row->required_document ? $row->required_document->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'beneficiary', 'file', 'required_document']);

            return $table->make(true);
        }

        return view('admin.beneficiaryFiles.index');
    }

    public function create()
    {
        abort_if(Gate::denies('beneficiary_file_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaries = Beneficiary::pluck('dob', 'id')->prepend(trans('global.pleaseSelect'), '');

        $required_documents = RequiredDocument::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.beneficiaryFiles.create', compact('beneficiaries', 'required_documents'));
    }

    public function store(StoreBeneficiaryFileRequest $request)
    {
        $beneficiaryFile = BeneficiaryFile::create($request->all());

        if ($request->input('file', false)) {
            $beneficiaryFile->addMedia(storage_path('tmp/uploads/' . basename($request->input('file'))))->toMediaCollection('file');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $beneficiaryFile->id]);
        }

        return redirect()->route('admin.beneficiary-files.index');
    }

    public function edit(BeneficiaryFile $beneficiaryFile)
    {
        abort_if(Gate::denies('beneficiary_file_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaries = Beneficiary::pluck('dob', 'id')->prepend(trans('global.pleaseSelect'), '');

        $required_documents = RequiredDocument::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiaryFile->load('beneficiary', 'required_document');

        return view('admin.beneficiaryFiles.edit', compact('beneficiaries', 'beneficiaryFile', 'required_documents'));
    }

    public function update(UpdateBeneficiaryFileRequest $request, BeneficiaryFile $beneficiaryFile)
    {
        $beneficiaryFile->update($request->all());

        if ($request->input('file', false)) {
            if (! $beneficiaryFile->file || $request->input('file') !== $beneficiaryFile->file->file_name) {
                if ($beneficiaryFile->file) {
                    $beneficiaryFile->file->delete();
                }
                $beneficiaryFile->addMedia(storage_path('tmp/uploads/' . basename($request->input('file'))))->toMediaCollection('file');
            }
        } elseif ($beneficiaryFile->file) {
            $beneficiaryFile->file->delete();
        }

        return redirect()->route('admin.beneficiary-files.index');
    }

    public function show(BeneficiaryFile $beneficiaryFile)
    {
        abort_if(Gate::denies('beneficiary_file_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaryFile->load('beneficiary', 'required_document');

        return view('admin.beneficiaryFiles.show', compact('beneficiaryFile'));
    }

    public function destroy(BeneficiaryFile $beneficiaryFile)
    {
        abort_if(Gate::denies('beneficiary_file_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaryFile->delete();

        return back();
    }

    public function massDestroy(MassDestroyBeneficiaryFileRequest $request)
    {
        $beneficiaryFiles = BeneficiaryFile::find(request('ids'));

        foreach ($beneficiaryFiles as $beneficiaryFile) {
            $beneficiaryFile->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('beneficiary_file_create') && Gate::denies('beneficiary_file_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new BeneficiaryFile();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
