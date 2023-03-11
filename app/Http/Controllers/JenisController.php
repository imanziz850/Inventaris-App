<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use Illuminate\Http\Request;

class JenisController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $jenis = Jenis::when($search, function ($query, $search) {
            return $query->where('kode_jenis', 'like', "%{$search}%")
                ->orWhere('nama_jenis', 'like', "%{$search}%");
        })->paginate();

        if ($search) {
            $jenis->appends(['search' => '$search']);
        }
        return view('jenis.index', [
            'jenis' => $jenis,
        ]);
    }
    public function create()
    {
        return view('jenis.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_jenis' => 'required|max:10|alpha_num|unique:jenis',
            'nama_jenis' => 'required|max:255',
            'keterangan' => 'nullable|max:255'
        ]);

        $request->merge([
            'kode_jenis' => strtoupper(strtolower($request->kode_jenis))
        ]);

        Jenis::create($request->all());

        return redirect()->route('jenis.index')
            ->with('message', 'success store');
    }
    public function show()
    {
        return abort(404);
    }
    public function edit(Jenis $jeni)
    {
        return view('jenis.edit', [
            'jenis' => $jeni
        ]);
    }
    public function update(Request $request, Jenis $jeni)
    {
        $request->validate([
            'kode_jenis' => 'required|max:10|alpha_num|unique:jenis,kode_jenis,' . $jeni->id,
            'nama_jenis' => 'required|max:255',
            'keterangan' => 'nullable|max:255'
        ]);

        $request->merge([
            'kode_jenis' => strtoupper(strtolower($request->kode_jenis))
        ]);

        $jeni->update($request->all());
        
        return redirect()->route('jenis.index')
            ->with('message', 'success delete');
    }
    public function destroy(Jenis $jeni)
    {
        $jeni->delete();

        return back()->with('message', 'success delete');
    }
}
