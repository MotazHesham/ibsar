<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyBuildingBeneficiaryRequest;
use App\Http\Requests\Admin\StoreBuildingBeneficiaryRequest;
use App\Http\Requests\Admin\UpdateBuildingBeneficiaryRequest;
use App\Models\Beneficiary;
use App\Models\Building;
use App\Models\BuildingBeneficiary;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BuildingBeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('building_beneficiary_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BuildingBeneficiary::with(['building', 'beneficiary'])->select(sprintf('%s.*', (new BuildingBeneficiary)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'building_beneficiary_show';
                $editGate      = 'building_beneficiary_edit';
                $deleteGate    = 'building_beneficiary_delete';
                $crudRoutePart = 'building-beneficiaries';

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
            $table->addColumn('building_name', function ($row) {
                return $row->building ? $row->building->name : '';
            });

            $table->addColumn('beneficiary_dob', function ($row) {
                return $row->beneficiary ? $row->beneficiary->dob : '';
            });

            $table->editColumn('notes', function ($row) {
                return $row->notes ? $row->notes : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'building', 'beneficiary']);

            return $table->make(true);
        }

        return view('admin.buildingBeneficiaries.index');
    }

    public function create()
    {
        abort_if(Gate::denies('building_beneficiary_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $buildings = Building::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiaries = Beneficiary::pluck('dob', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.buildingBeneficiaries.create', compact('beneficiaries', 'buildings'));
    }

    public function store(StoreBuildingBeneficiaryRequest $request)
    {
        $buildingBeneficiary = BuildingBeneficiary::create($request->all());

        return redirect()->route('admin.building-beneficiaries.index');
    }

    public function edit(BuildingBeneficiary $buildingBeneficiary)
    {
        abort_if(Gate::denies('building_beneficiary_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $buildings = Building::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiaries = Beneficiary::pluck('dob', 'id')->prepend(trans('global.pleaseSelect'), '');

        $buildingBeneficiary->load('building', 'beneficiary');

        return view('admin.buildingBeneficiaries.edit', compact('beneficiaries', 'buildingBeneficiary', 'buildings'));
    }

    public function update(UpdateBuildingBeneficiaryRequest $request, BuildingBeneficiary $buildingBeneficiary)
    {
        $buildingBeneficiary->update($request->all());

        return redirect()->route('admin.building-beneficiaries.index');
    }

    public function show(BuildingBeneficiary $buildingBeneficiary)
    {
        abort_if(Gate::denies('building_beneficiary_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $buildingBeneficiary->load('building', 'beneficiary');

        return view('admin.buildingBeneficiaries.show', compact('buildingBeneficiary'));
    }

    public function destroy(BuildingBeneficiary $buildingBeneficiary)
    {
        abort_if(Gate::denies('building_beneficiary_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $buildingBeneficiary->delete();

        return back();
    }

    public function massDestroy(MassDestroyBuildingBeneficiaryRequest $request)
    {
        $buildingBeneficiaries = BuildingBeneficiary::find(request('ids'));

        foreach ($buildingBeneficiaries as $buildingBeneficiary) {
            $buildingBeneficiary->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
