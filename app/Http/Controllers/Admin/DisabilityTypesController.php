<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyDisabilityTypeRequest;
use App\Http\Requests\Admin\StoreDisabilityTypeRequest;
use App\Http\Requests\Admin\UpdateDisabilityTypeRequest;
use App\Models\DisabilityType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DisabilityTypesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('disability_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DisabilityType::query()->select(sprintf('%s.*', (new DisabilityType)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'disability_type_show';
                $editGate      = 'disability_type_edit';
                $deleteGate    = 'disability_type_delete';
                $crudRoutePart = 'disability-types';

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

        return view('admin.disabilityTypes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('disability_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.disabilityTypes.create');
    }

    public function store(StoreDisabilityTypeRequest $request)
    {
        $disabilityType = DisabilityType::create($request->all());

        return redirect()->route('admin.disability-types.index');
    }

    public function edit(DisabilityType $disabilityType)
    {
        abort_if(Gate::denies('disability_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.disabilityTypes.edit', compact('disabilityType'));
    }

    public function update(UpdateDisabilityTypeRequest $request, DisabilityType $disabilityType)
    { 
        $disabilityType->setTranslation('name', $request->lang, $request->name);
        $disabilityType->save();

        return redirect()->route('admin.disability-types.index');
    }

    public function show(DisabilityType $disabilityType)
    {
        abort_if(Gate::denies('disability_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.disabilityTypes.show', compact('disabilityType'));
    }

    public function destroy(DisabilityType $disabilityType)
    {
        abort_if(Gate::denies('disability_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $disabilityType->delete();

        return back();
    }

    public function massDestroy(MassDestroyDisabilityTypeRequest $request)
    {
        $disabilityTypes = DisabilityType::find(request('ids'));

        foreach ($disabilityTypes as $disabilityType) {
            $disabilityType->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
