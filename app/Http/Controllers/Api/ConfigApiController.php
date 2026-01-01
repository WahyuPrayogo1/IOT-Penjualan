<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Rule;

class ConfigApiController extends Controller
{
    /**
     * API ringan untuk cek versi saja
     * Dipanggil extension berdasarkan TTL
     */
    public function version()
    {
        $config = Config::first();

        return response()->json([
            'version' => $config?->version ?? '0.0'
        ]);
    }

    /**
     * API full config + rules
     * Dipanggil HANYA kalau versi berbeda
     */
    public function index()
    {
        $config = Config::first();

        if (!$config) {
            return response()->json([
                'message' => 'Config not found'
            ], 404);
        }

        $rules = Rule::where('is_active', true)->get();

        return response()->json([
            'version' => $config->version,
            'threshold' => [
                'warning' => $config->warning_threshold,
                'block'   => $config->block_threshold,
            ],
            'ttl_hours' => $config->ttl_hours,

            // RULES
            'keywords' => $rules->where('type', 'keyword')->pluck('value')->values(),
            'phrases'  => $rules->where('type', 'phrase')->pluck('value')->values(),
            'safe_patterns' => $rules->where('type', 'safe')->pluck('value')->values(),
        ]);
    }
}
