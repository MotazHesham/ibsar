<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyHealthConditionRequest;
use App\Http\Requests\Admin\StoreHealthConditionRequest;
use App\Http\Requests\Admin\UpdateHealthConditionRequest;
use App\Models\HealthCondition;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class HealthConditionsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('health_condition_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = HealthCondition::query()->select(sprintf('%s.*', (new HealthCondition)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'health_condition_show';
                $editGate      = 'health_condition_edit';
                $deleteGate    = 'health_condition_delete';
                $crudRoutePart = 'health-conditions';

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

        return view('admin.healthConditions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('health_condition_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.healthConditions.create');
    }

    public function store(StoreHealthConditionRequest $request)
    {
        $healthCondition = HealthCondition::create($request->all());

        return redirect()->route('admin.health-conditions.index');
    }

    public function edit(HealthCondition $healthCondition)
    {
        abort_if(Gate::denies('health_condition_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.healthConditions.edit', compact('healthCondition'));
    }

    public function update(UpdateHealthConditionRequest $request, HealthCondition $healthCondition)
    { 
        $healthCondition->setTranslation('name', $request->lang, $request->name);
        $healthCondition->save();

        return redirect()->route('admin.health-conditions.index');
    }

    public function show(HealthCondition $healthCondition)
    {
        abort_if(Gate::denies('health_condition_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.healthConditions.show', compact('healthCondition'));
    }

    public function destroy(HealthCondition $healthCondition)
    {
        abort_if(Gate::denies('health_condition_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $healthCondition->delete();

        return back();
    }

    public function massDestroy(MassDestroyHealthConditionRequest $request)
    {
        $healthConditions = HealthCondition::find(request('ids'));

        foreach ($healthConditions as $healthCondition) {
            $healthCondition->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
