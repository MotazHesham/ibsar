<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyMaritalStatusRequest;
use App\Http\Requests\Admin\StoreMaritalStatusRequest;
use App\Http\Requests\Admin\UpdateMaritalStatusRequest;
use App\Models\MaritalStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MaritalStatusController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('marital_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MaritalStatus::query()->select(sprintf('%s.*', (new MaritalStatus)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'marital_status_show';
                $editGate      = 'marital_status_edit';
                $deleteGate    = 'marital_status_delete';
                $crudRoutePart = 'marital-statuses';

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

        return view('admin.maritalStatuses.index');
    }

    public function create()
    {
        abort_if(Gate::denies('marital_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.maritalStatuses.create');
    }

    public function store(StoreMaritalStatusRequest $request)
    {
        $maritalStatus = MaritalStatus::create($request->all());

        return redirect()->route('admin.marital-statuses.index');
    }

    public function edit(MaritalStatus $maritalStatus)
    {
        abort_if(Gate::denies('marital_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.maritalStatuses.edit', compact('maritalStatus'));
    }

    public function update(UpdateMaritalStatusRequest $request, MaritalStatus $maritalStatus)
    {
        $maritalStatus->setTranslation('name', $request->lang, $request->name);
        $maritalStatus->save();

        return redirect()->route('admin.marital-statuses.index');
    }

    public function show(MaritalStatus $maritalStatus)
    {
        abort_if(Gate::denies('marital_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.maritalStatuses.show', compact('maritalStatus'));
    }

    public function destroy(MaritalStatus $maritalStatus)
    {
        abort_if(Gate::denies('marital_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $maritalStatus->delete();

        return back();
    }

    public function massDestroy(MassDestroyMaritalStatusRequest $request)
    {
        $maritalStatuses = MaritalStatus::find(request('ids'));

        foreach ($maritalStatuses as $maritalStatus) {
            $maritalStatus->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
