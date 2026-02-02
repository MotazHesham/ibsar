<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Section;
use App\Models\Role;
use App\Models\User;
use App\Models\MaritalStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    use MediaUploadingTrait;
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = User::where('user_type','staff')->with(['roles'])->select(sprintf('%s.*', (new User)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'user_show';
                $editGate      = $row->id == 1 ? '' : 'user_edit';
                $deleteGate    = $row->id == 1 ? '' : 'user_delete';
                $crudRoutePart = 'users';

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
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('username', function ($row) {
                return $row->username ? $row->username : '';
            });
            $table->editColumn('phone', function ($row) {
                return $row->phone ? $row->phone : '';
            });
            $table->editColumn('approved', function ($row) { 
                $checked = $row->approved ? 'checked' : '';
                return '<div class="custom-toggle-switch toggle-md ms-2">
                    <input onchange="updateStatuses(this, \'approved\', \'' . addslashes("App\Models\User") . '\')" 
                        value="' . $row->id . '"  id="approved-' . $row->id . '" type="checkbox" ' . $checked . '>
                    <label for="approved-' . $row->id . '" class="label-success mb-2"></label>
                </div>'; 
            });
            $table->editColumn('roles', function ($row) {
                $labels = [];
                foreach ($row->roles as $role) {
                    $labels[] = sprintf('<span class="badge bg-primary-transparent">%s</span>', $role->title);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('identity_num', function ($row) {
                return $row->identity_num ? $row->identity_num : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'approved', 'roles']);

            return $table->make(true);
        }

        return view('admin.users.index');
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');
        $sections = Section::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $maritalStatuses = MaritalStatus::pluck('name', 'id');

        return view('admin.users.create', compact('roles', 'sections', 'maritalStatuses'));
    }

    public function store(StoreUserRequest $request)
    {
        $requestData = $request->all();
        $requestData['user_type'] = 'staff';
        $user = User::create($requestData);
        $user->roles()->sync($request->input('roles', []));
        $user->maritalStatuses()->sync($request->input('marital_statuses', []));

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');
        $sections = Section::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $maritalStatuses = MaritalStatus::pluck('name', 'id');

        $user->load('roles');
        return view('admin.users.edit', compact('roles', 'user', 'sections', 'maritalStatuses'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));
        $user->maritalStatuses()->sync($request->input('marital_statuses', []));
        
        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles', 'userUserAlerts');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($user->id == 1) {
            session()->flash('error_message', 'cant delete user admin');
            return back();
        }
        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        $users = User::find(request('ids'));

        foreach ($users as $user) {
            if ($user->id == 1) {
                session()->flash('error_message', 'cant delete user admin');
                return back();
            }
            $user->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
