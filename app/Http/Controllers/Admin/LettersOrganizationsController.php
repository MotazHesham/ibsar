<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyLettersOrganizationRequest;
use App\Http\Requests\Admin\StoreLettersOrganizationRequest;
use App\Http\Requests\Admin\UpdateLettersOrganizationRequest;
use App\Models\LettersOrganization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LettersOrganizationsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('letters_organization_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = LettersOrganization::query()->select(sprintf('%s.*', (new LettersOrganization)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'letters_organization_show';
                $editGate      = 'letters_organization_edit';
                $deleteGate    = 'letters_organization_delete';
                $crudRoutePart = 'letters-organizations';

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
            $table->editColumn('contact_person', function ($row) {
                return $row->contact_person ? $row->contact_person : '';
            });
            $table->editColumn('contact_phone', function ($row) {
                return $row->contact_phone ? $row->contact_phone : '';
            });
            $table->editColumn('contact_email', function ($row) {
                return $row->contact_email ? $row->contact_email : '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.lettersOrganizations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('letters_organization_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lettersOrganizations.create');
    }

    public function store(StoreLettersOrganizationRequest $request)
    {
        $lettersOrganization = LettersOrganization::create($request->all());

        return redirect()->route('admin.letters-organizations.index');
    }

    public function edit(LettersOrganization $lettersOrganization)
    {
        abort_if(Gate::denies('letters_organization_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lettersOrganizations.edit', compact('lettersOrganization'));
    }

    public function update(UpdateLettersOrganizationRequest $request, LettersOrganization $lettersOrganization)
    {
        $lettersOrganization->update($request->all());

        return redirect()->route('admin.letters-organizations.index');
    }

    public function show(LettersOrganization $lettersOrganization)
    {
        abort_if(Gate::denies('letters_organization_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lettersOrganizations.show', compact('lettersOrganization'));
    }

    public function destroy(LettersOrganization $lettersOrganization)
    {
        abort_if(Gate::denies('letters_organization_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lettersOrganization->delete();

        return back();
    }

    public function massDestroy(MassDestroyLettersOrganizationRequest $request)
    {
        $lettersOrganizations = LettersOrganization::find(request('ids'));

        foreach ($lettersOrganizations as $lettersOrganization) {
            $lettersOrganization->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
