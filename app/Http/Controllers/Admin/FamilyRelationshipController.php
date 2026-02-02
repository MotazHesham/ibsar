<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyFamilyRelationshipRequest;
use App\Http\Requests\Admin\StoreFamilyRelationshipRequest;
use App\Http\Requests\Admin\UpdateFamilyRelationshipRequest;
use App\Models\FamilyRelationship;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FamilyRelationshipController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('family_relationship_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FamilyRelationship::query()->select(sprintf('%s.*', (new FamilyRelationship)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'family_relationship_show';
                $editGate      = 'family_relationship_edit';
                $deleteGate    = 'family_relationship_delete';
                $crudRoutePart = 'family-relationships';

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
            $table->editColumn('gender', function ($row) {
                return $row->gender ? FamilyRelationship::GENDER_RADIO[$row->gender] : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.familyRelationships.index');
    }

    public function create()
    {
        abort_if(Gate::denies('family_relationship_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.familyRelationships.create');
    }

    public function store(StoreFamilyRelationshipRequest $request)
    {
        $familyRelationship = FamilyRelationship::create($request->all());

        return redirect()->route('admin.family-relationships.index');
    }

    public function edit(FamilyRelationship $familyRelationship)
    {
        abort_if(Gate::denies('family_relationship_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.familyRelationships.edit', compact('familyRelationship'));
    }

    public function update(UpdateFamilyRelationshipRequest $request, FamilyRelationship $familyRelationship)
    {
        $familyRelationship->update($request->except('lang','name'));
        $familyRelationship->setTranslation('name', $request->lang, $request->name);
        $familyRelationship->save();

        return redirect()->route('admin.family-relationships.index');
    }

    public function show(FamilyRelationship $familyRelationship)
    {
        abort_if(Gate::denies('family_relationship_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.familyRelationships.show', compact('familyRelationship'));
    }

    public function destroy(FamilyRelationship $familyRelationship)
    {
        abort_if(Gate::denies('family_relationship_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $familyRelationship->delete();

        return back();
    }

    public function massDestroy(MassDestroyFamilyRelationshipRequest $request)
    {
        $familyRelationships = FamilyRelationship::find(request('ids'));

        foreach ($familyRelationships as $familyRelationship) {
            $familyRelationship->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
