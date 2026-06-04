<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function currency(): JsonResponse
    {
        return response()->json([
            'usd_to_sp_rate' => usd_to_sp_rate(),
        ]);
    }

    public function updateCurrency(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'usd_to_sp_rate' => ['required', 'numeric', 'min:1', 'max:10000000'],
        ]);

        Setting::set('usd_to_sp_rate', $validated['usd_to_sp_rate']);

        return response()->json([
            'usd_to_sp_rate' => usd_to_sp_rate(),
            'message' => 'Exchange rate updated.',
        ]);
    }
}
