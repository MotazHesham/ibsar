<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\UserAlert;
use Illuminate\Http\Request;

class UserAlertsController extends Controller
{
    public function index(Request $request)
    {
        $alerts = auth()->user()
            ->userUserAlerts()
            ->latest()
            ->limit(20)
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'count' => $alerts->count(),
                'items' => $alerts->map(fn (UserAlert $alert) => [
                    'id' => $alert->id,
                    'text' => $alert->alert_text,
                    'link' => $alert->alert_link,
                    'at' => $alert->created_at?->format('Y-m-d H:i'),
                ]),
            ]);
        }

        return view('beneficiary.user-alerts.index', compact('alerts'));
    }
}
