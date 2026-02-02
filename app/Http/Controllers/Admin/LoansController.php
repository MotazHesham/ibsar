<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyLoanRequest;
use App\Http\Requests\Admin\StoreLoanRequest;
use App\Http\Requests\Admin\UpdateLoanRequest;
use App\Models\Loan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LoansController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('loan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Loan::query()->select(sprintf('%s.*', (new Loan)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'loan_show';
                $editGate      = 'loan_edit';
                $deleteGate    = 'loan_delete';
                $crudRoutePart = 'loans';

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
            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.loans.index');
    }

    public function create()
    {
        abort_if(Gate::denies('loan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.loans.create');
    }

    public function store(StoreLoanRequest $request)
    {
        $loan = Loan::create($request->all());

        return redirect()->route('admin.loans.index');
    }

    public function edit(Loan $loan)
    {
        abort_if(Gate::denies('loan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.loans.edit', compact('loan'));
    }

    public function update(UpdateLoanRequest $request, Loan $loan)
    { 
        $loan->update($request->all());

        return redirect()->route('admin.loans.index');
    }

    public function show(Loan $loan)
    {
        abort_if(Gate::denies('loan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.loans.show', compact('loan'));
    }

    public function destroy(Loan $loan)
    {
        abort_if(Gate::denies('loan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loan->delete();

        return back();
    }

    public function massDestroy(MassDestroyLoanRequest $request)
    {
        $loans = Loan::find(request('ids'));

        foreach ($loans as $loan) {
            $loan->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
