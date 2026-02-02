<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyNationalityRequest;
use App\Http\Requests\Admin\StoreNationalityRequest;
use App\Http\Requests\Admin\UpdateNationalityRequest;
use App\Models\Nationality;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class NationalitiesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('nationality_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Nationality::query()->select(sprintf('%s.*', (new Nationality)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'nationality_show';
                $editGate      = 'nationality_edit';
                $deleteGate    = 'nationality_delete';
                $crudRoutePart = 'nationalities';

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

        return view('admin.nationalities.index');
    }

    public function create()
    {
        abort_if(Gate::denies('nationality_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.nationalities.create');
    }

    public function store(StoreNationalityRequest $request)
    {
        $nationality = Nationality::create($request->all());

        return redirect()->route('admin.nationalities.index');
    }

    public function edit(Nationality $nationality)
    {
        abort_if(Gate::denies('nationality_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.nationalities.edit', compact('nationality'));
    }

    public function update(UpdateNationalityRequest $request, Nationality $nationality)
    { 
        $nationality->setTranslation('name', $request->lang, $request->name);
        $nationality->save();

        return redirect()->route('admin.nationalities.index');
    }

    public function show(Nationality $nationality)
    {
        abort_if(Gate::denies('nationality_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.nationalities.show', compact('nationality'));
    }

    public function destroy(Nationality $nationality)
    {
        abort_if(Gate::denies('nationality_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $nationality->delete();

        return back();
    }

    public function massDestroy(MassDestroyNationalityRequest $request)
    {
        $nationalities = Nationality::find(request('ids'));

        foreach ($nationalities as $nationality) {
            $nationality->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
