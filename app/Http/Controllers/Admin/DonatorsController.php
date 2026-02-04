<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyDonatorRequest;
use App\Http\Requests\Admin\StoreDonatorRequest;
use App\Http\Requests\Admin\UpdateDonatorRequest;
use App\Models\Donator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DonatorsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('donator_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Donator::query()->select(sprintf('%s.*', (new Donator)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'donator_show';
                $editGate      = 'donator_edit';
                $deleteGate    = 'donator_delete';
                $crudRoutePart = 'donators';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?: '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ?: '';
            });

            $table->editColumn('email', function ($row) {
                return $row->email ?: '';
            });

            $table->editColumn('phone', function ($row) {
                return $row->phone ?: '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.donators.index');
    }

    public function create()
    {
        abort_if(Gate::denies('donator_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.donators.create');
    }

    public function store(StoreDonatorRequest $request)
    {
        Donator::create($request->all());

        return redirect()->route('admin.donators.index');
    }

    public function edit(Donator $donator)
    {
        abort_if(Gate::denies('donator_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.donators.edit', compact('donator'));
    }

    public function update(UpdateDonatorRequest $request, Donator $donator)
    {
        $donator->update($request->all());

        return redirect()->route('admin.donators.index');
    }

    public function show(Donator $donator)
    {
        abort_if(Gate::denies('donator_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $donator->load('donations');

        return view('admin.donators.show', compact('donator'));
    }

    public function destroy(Donator $donator)
    {
        abort_if(Gate::denies('donator_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $donator->delete();

        return back();
    }

    public function massDestroy(MassDestroyDonatorRequest $request)
    {
        $donators = Donator::find($request->input('ids', []));

        foreach ($donators as $donator) {
            $donator->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

