<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyEconomicStatusRequest;
use App\Http\Requests\Admin\StoreEconomicStatusRequest;
use App\Http\Requests\Admin\UpdateEconomicStatusRequest;
use App\Models\EconomicStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class EconomicStatusController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('economic_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = EconomicStatus::query()->select(sprintf('%s.*', (new EconomicStatus)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'economic_status_show';
                $editGate      = 'economic_status_edit';
                $deleteGate    = 'economic_status_delete';
                $crudRoutePart = 'economic-statuses';

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
            $table->editColumn('type', function ($row) {
                return $row->type ? EconomicStatus::TYPE_SELECT[$row->type] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.economicStatuses.index');
    }

    public function create()
    {
        abort_if(Gate::denies('economic_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.economicStatuses.create');
    }

    public function store(StoreEconomicStatusRequest $request)
    {
        $economicStatus = EconomicStatus::create($request->all());

        return redirect()->route('admin.economic-statuses.index');
    }

    public function edit(EconomicStatus $economicStatus)
    {
        abort_if(Gate::denies('economic_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.economicStatuses.edit', compact('economicStatus'));
    }

    public function update(UpdateEconomicStatusRequest $request, EconomicStatus $economicStatus)
    {
        $economicStatus->update($request->except('lang','name'));
        $economicStatus->setTranslation('name', $request->lang, $request->name);
        $economicStatus->save();

        return redirect()->route('admin.economic-statuses.index');
    }

    public function show(EconomicStatus $economicStatus)
    {
        abort_if(Gate::denies('economic_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.economicStatuses.show', compact('economicStatus'));
    }

    public function destroy(EconomicStatus $economicStatus)
    {
        abort_if(Gate::denies('economic_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $economicStatus->delete();

        return back();
    }

    public function massDestroy(MassDestroyEconomicStatusRequest $request)
    {
        $economicStatuses = EconomicStatus::find(request('ids'));

        foreach ($economicStatuses as $economicStatus) {
            $economicStatus->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
