<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyBeneficiaryCategoryRequest;
use App\Http\Requests\Admin\StoreBeneficiaryCategoryRequest;
use App\Http\Requests\Admin\UpdateBeneficiaryCategoryRequest;
use App\Models\BeneficiaryCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BeneficiaryCategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('beneficiary_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BeneficiaryCategory::query()->select(sprintf('%s.*', (new BeneficiaryCategory)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'beneficiary_category_show';
                $editGate      = 'beneficiary_category_edit';
                $deleteGate    = 'beneficiary_category_delete';
                $crudRoutePart = 'beneficiary-categories';

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

        return view('admin.beneficiaryCategories.index');
    }

    public function create()
    {
        abort_if(Gate::denies('beneficiary_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.beneficiaryCategories.create');
    }

    public function store(StoreBeneficiaryCategoryRequest $request)
    {
        $beneficiaryCategory = BeneficiaryCategory::create($request->all());

        return redirect()->route('admin.beneficiary-categories.index');
    }

    public function edit(BeneficiaryCategory $beneficiaryCategory)
    {
        abort_if(Gate::denies('beneficiary_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.beneficiaryCategories.edit', compact('beneficiaryCategory'));
    }

    public function update(UpdateBeneficiaryCategoryRequest $request, BeneficiaryCategory $beneficiaryCategory)
    {
        $beneficiaryCategory->setTranslation('name', $request->lang, $request->name);
        $beneficiaryCategory->save();

        return redirect()->route('admin.beneficiary-categories.index');
    }

    public function show(BeneficiaryCategory $beneficiaryCategory)
    {
        abort_if(Gate::denies('beneficiary_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.beneficiaryCategories.show', compact('beneficiaryCategory'));
    }

    public function destroy(BeneficiaryCategory $beneficiaryCategory)
    {
        abort_if(Gate::denies('beneficiary_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaryCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyBeneficiaryCategoryRequest $request)
    {
        $beneficiaryCategories = BeneficiaryCategory::find(request('ids'));

        foreach ($beneficiaryCategories as $beneficiaryCategory) {
            $beneficiaryCategory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

