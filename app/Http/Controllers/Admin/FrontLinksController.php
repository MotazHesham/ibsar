<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyFrontLinkRequest;
use App\Http\Requests\Admin\StoreFrontLinkRequest;
use App\Http\Requests\Admin\UpdateFrontLinkRequest;
use App\Models\FrontLink;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FrontLinksController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('front_link_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FrontLink::query()->select(sprintf('%s.*', (new FrontLink)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'front_link_show';
                $editGate      = 'front_link_edit';
                $deleteGate    = 'front_link_delete';
                $crudRoutePart = 'front-links';

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
            $table->editColumn('link', function ($row) {
                return $row->link ? $row->link : '';
            });
            $table->editColumn('position', function ($row) {
                return $row->position ? FrontLink::POSITION_SELECT[$row->position] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.frontLinks.index');
    }

    public function create()
    {
        abort_if(Gate::denies('front_link_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontLinks.create');
    }

    public function store(StoreFrontLinkRequest $request)
    {
        $frontLink = FrontLink::create($request->all());

        return redirect()->route('admin.front-links.index');
    }

    public function edit(FrontLink $frontLink)
    {
        abort_if(Gate::denies('front_link_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontLinks.edit', compact('frontLink'));
    }

    public function update(UpdateFrontLinkRequest $request, FrontLink $frontLink)
    {
        $frontLink->update($request->all());

        return redirect()->route('admin.front-links.index');
    }

    public function show(FrontLink $frontLink)
    {
        abort_if(Gate::denies('front_link_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontLinks.show', compact('frontLink'));
    }

    public function destroy(FrontLink $frontLink)
    {
        abort_if(Gate::denies('front_link_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $frontLink->delete();

        return back();
    }

    public function massDestroy(MassDestroyFrontLinkRequest $request)
    {
        $frontLinks = FrontLink::find(request('ids'));

        foreach ($frontLinks as $frontLink) {
            $frontLink->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
