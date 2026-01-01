<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function edit()
    {
        // Selalu pastikan config ada 1 baris
        $config = Config::firstOrCreate(
            [],
            [
                'version' => '0.1',
                'warning_threshold' => 30,
                'block_threshold' => 60,
                'ttl_hours' => 24,
            ]
        );

        return view('backend.configs.form', [
            'config' => $config,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, Config $config)
    {
        $data = $request->validate([
            'warning_threshold' => 'required|integer|min:0|max:100',
            'block_threshold'   => 'required|integer|min:0|max:100',
            'ttl_hours'         => 'required|integer|min:1|max:168',
        ]);

        $config->update($data);

        return redirect()
            ->back()
            ->with('success', 'Configuration updated successfully.');
    }
}
