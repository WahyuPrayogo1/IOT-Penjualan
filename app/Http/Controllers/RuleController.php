<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\Config;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RuleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rules = Rule::query();

            return DataTables::of($rules)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('rules.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>

                        <form action="'.route('rules.destroy', $row->id).'" method="POST" style="display:inline-block">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm(\'Hapus rule ini?\')">
                                Delete
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.rules.index');
    }

    public function create()
    {
        return view('backend.rules.form', [
            'isEdit' => false
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'  => 'required|in:keyword,phrase,safe',
            'value' => 'required|string|max:255',
        ]);

        $data['value'] = strtolower($data['value']);
        $data['is_active'] = true;

        Rule::create($data);

        $this->bumpConfigVersion();

        return redirect()
            ->route('rules.index')
            ->with('success', 'Rule berhasil ditambahkan.');
    }

    public function edit(Rule $rule)
    {
        return view('backend.rules.form', [
            'rule'   => $rule,
            'isEdit' => true
        ]);
    }

public function update(Request $request, Rule $rule)
{
    $data = $request->validate([
        'type'      => 'required|in:keyword,phrase,safe',
        'value'     => 'required|string|max:255',
        'is_active' => 'required|boolean', // 🔑 WAJIB
    ]);

    $data['value'] = strtolower($data['value']);

    $rule->update($data);

    $this->bumpConfigVersion();

    return redirect()
        ->route('rules.index')
        ->with('success', 'Rule berhasil diperbarui.');
}

    public function destroy(Rule $rule)
    {
        $rule->delete();

        $this->bumpConfigVersion();

        return redirect()
            ->route('rules.index')
            ->with('success', 'Rule berhasil dihapus.');
    }

    /**
     * Naikkan versi config setiap ada perubahan rule
     */
    private function bumpConfigVersion()
    {
        $config = Config::first();

        if ($config) {
            $config->update([
                'version' => number_format(((float) $config->version) + 0.1, 1)
            ]);
        }
    }
}
