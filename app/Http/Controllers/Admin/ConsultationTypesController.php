<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyConsultationTypeRequest;
use App\Http\Requests\Admin\StoreConsultationTypeRequest;
use App\Http\Requests\Admin\UpdateConsultationTypeRequest;
use App\Models\ConsultationType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ConsultationTypesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('consultation_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ConsultationType::query();
            $query->select(sprintf('%s.*', (new ConsultationType)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'consultation_type_show';
                $editGate      = 'consultation_type_edit';
                $deleteGate    = 'consultation_type_delete';
                $crudRoutePart = 'consultation-types';

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.consultationTypes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('consultation_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.consultationTypes.create');
    }

    public function store(StoreConsultationTypeRequest $request)
    {
        $consultationType = ConsultationType::create($request->all());

        return redirect()->route('admin.consultation-types.index');
    }

    public function edit(ConsultationType $consultationType)
    {
        abort_if(Gate::denies('consultation_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.consultationTypes.edit', compact('consultationType'));
    }

    public function update(UpdateConsultationTypeRequest $request, ConsultationType $consultationType)
    {
        $consultationType->update($request->all());

        return redirect()->route('admin.consultation-types.index');
    }

    public function show(ConsultationType $consultationType)
    {
        abort_if(Gate::denies('consultation_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultationType->load('consultants');

        return view('admin.consultationTypes.show', compact('consultationType'));
    }

    public function destroy(ConsultationType $consultationType)
    {
        abort_if(Gate::denies('consultation_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultationType->delete();

        return back();
    }

    public function massDestroy(MassDestroyConsultationTypeRequest $request)
    {
        $consultationTypes = ConsultationType::find(request('ids'));

        foreach ($consultationTypes as $consultationType) {
            $consultationType->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
} 