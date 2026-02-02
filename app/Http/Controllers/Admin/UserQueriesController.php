<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserQueryRequest;
use App\Models\User;
use App\Models\UserQuery;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class UserQueriesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_query_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = UserQuery::with(['user'])->select(sprintf('%s.*', (new UserQuery)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'user_query_show';
                $editGate      = 'user_query_edit';
                $deleteGate    = 'user_query_delete';
                $crudRoutePart = 'user-queries';

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
            $table->editColumn('question', function ($row) {
                return $row->question ? $row->question : '';
            });
            $table->editColumn('answer', function ($row) {
                return $row->answer ? $row->answer : '';
            });
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user']);

            return $table->make(true);
        }

        return view('admin.userQueries.index');
    }

    public function edit(UserQuery $userQuery)
    {
        abort_if(Gate::denies('user_query_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $userQuery->load('user');

        return view('admin.userQueries.edit', compact('userQuery', 'users'));
    }

    public function update(UpdateUserQueryRequest $request, UserQuery $userQuery)
    {
        $userQuery->update($request->all());

        return redirect()->route('admin.user-queries.index');
    }
}
