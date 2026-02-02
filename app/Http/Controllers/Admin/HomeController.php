<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\BeneficiaryOrder;
use App\Models\MaritalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{
    public function index()
    { 

        $query = Beneficiary::query();
        
        if (Gate::denies('beneficiary_control_all_access')) {
            $query->where('specialist_id', auth()->id());
        }

        $total_beneficiaries = $query->select(DB::raw('
            COUNT(CASE WHEN profile_status = "uncompleted" AND is_archived = 0 THEN 1 END) as uncompleted,
            COUNT(CASE WHEN profile_status = "rejected" AND is_archived = 0 THEN 1 END) as rejected,
            COUNT(CASE WHEN profile_status IN ("request_join", "in_review") AND is_archived = 0 THEN 1 END) as pending,
            COUNT(CASE WHEN profile_status = "approved" AND is_archived = 0 THEN 1 END) as approved,
            COUNT(CASE WHEN is_archived = 1 THEN 1 END) as archived,
            COUNT(*) as total
        '))->first();

        $query = BeneficiaryOrder::query();
        if (Gate::denies('beneficiary_orders_management_access')) {
            $query->where('specialist_id', auth()->id());
        }

        $total_orders = $query->select(DB::raw('
            COUNT(CASE WHEN accept_status = "yes" AND done = 0 AND is_archived = 0 OR accept_status IS NULL AND done = 0 AND is_archived = 0 THEN 1 END) as current,
            COUNT(CASE WHEN accept_status = "yes" AND done = 1 AND is_archived = 0 THEN 1 END) as finished,
            COUNT(CASE WHEN accept_status = "no" AND is_archived = 0 THEN 1 END) as rejected,
            COUNT(CASE WHEN is_archived = 1 THEN 1 END) as archived,
            COUNT(*) as total
        '))->first();

        $orders_group_by_martial_status = DB::table('marital_statuses')
            ->leftJoin('beneficiaries', 'beneficiaries.marital_status_id', '=', 'marital_statuses.id')
            ->leftJoin('beneficiary_orders', 'beneficiary_orders.beneficiary_id', '=', 'beneficiaries.id')
            ->select('marital_statuses.name as status_name', DB::raw('count(beneficiary_orders.id) as order_count'))
            ->groupBy('marital_statuses.name')
            ->get();

        $query = BeneficiaryOrder::query();
        if (Gate::denies('beneficiary_orders_management_access')) {
            $query->where('specialist_id', auth()->id());
        }
        $latest_orders = $query->with('beneficiary.user', 'service')->orderBy('created_at', 'desc')->limit(5)->get();

        $query = Beneficiary::query();
        if (Gate::denies('beneficiary_control_all_access')) {
            $query->where('specialist_id', auth()->id());
        }
        $latest_beneficiaries = $query->with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        // get orders acceptance chart data group by month and for each month get the count of orders that accepted and the count of orders that rejected
        $orders_acceptance_chart_data = BeneficiaryOrder::whereYear('created_at', date('Y'))->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COUNT(CASE WHEN accept_status = "yes" THEN 1 END) as accepted'),
                DB::raw('COUNT(CASE WHEN accept_status = "no" THEN 1 END) as rejected'),
                DB::raw('COUNT(CASE WHEN done = 1 THEN 1 END) as done')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month'); // Key the collection by month for easier lookup

        $orders_acceptance_chart_data = collect(range(1, 12))->map(function ($month) use ($orders_acceptance_chart_data) {
            $data = $orders_acceptance_chart_data->firstWhere('month', $month);
            
            return [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'total_orders' => $data->total_orders ?? 0,
                'accepted' => $data->accepted ?? 0,
                'rejected' => $data->rejected ?? 0,
                'done' => $data->done ?? 0,
            ];
        }); 
        return view('admin.home', compact('total_beneficiaries', 'total_orders', 'orders_group_by_martial_status', 'latest_orders', 'latest_beneficiaries', 'orders_acceptance_chart_data'));
    }

    public function updateStatuses(Request $request)
    {
        $type = $request->type;
        $model = $request->model;
        $raw = $model::findOrFail($request->id);
        $raw->$type = $request->status;
        $raw->save();
        return 1;
    }
    public function arrange(Request $request)
    {
        $model = $request->model;
        $name = $request->name;
        $orderColumn = $request->orderColumn;

        $records = $model::orderBy($orderColumn, 'desc')->get();

        $html = view('partials.arrange', compact('records', 'model', 'name', 'orderColumn'))->render();
        return response()->json(['html' => $html]);
    }

    public function updateArrange(Request $request)
    {
        try {
            // Get JSON data from request
            $data = $request->json()->all();

            if (!isset($data['order']) || !is_array($data['order'])) {
                throw new \Exception('Invalid order data received');
            }

            $order = $data['order'];
            $orderColumn = $data['order_column'];
            $model = $data['model'];

            // Update each record's order
            foreach ($order as $item) {
                if (!isset($item['id']) || !isset($item['order'])) {
                    continue; // Skip invalid items
                }

                $model::where('id', $item['id'])
                    ->update([$orderColumn => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage()
            ], 500);
        }
    }
}
