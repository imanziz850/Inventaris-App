<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use Illuminate\Http\Request;

class RuangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $ruangs = Ruang::when($search, function ($query, $search) {
            return $query->where('kode_ruang', 'like', "%{$search}%")
                ->orWhere('nama_ruang', 'like', "%{$search}%");
        })->paginate();

        if ($search) {
            $ruangs->appends(['search' => $search]);
        }

        return view('ruang.index', [
            'ruangs' => $ruangs,
        ]);
    }
    public function create()
    {
        return view('ruang.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_ruang' => 'required|max:10|alpha_num|unique:ruangs',
            'nama_ruang' => 'required|max:255',
            'keterangan' => 'nullable|max:255'
        ]);

        $request->merge([
            'kode_ruang' => strtoupper(strtolower($request->kode_ruang))
        ]);

        Ruang::create($request->all());

        return redirect()->route('ruang.index')
            ->with('message', 'success store');
    }
    public function show()
    {
        return abort(404);
    }
    public function edit(Ruang $ruang)
    {
        return view('ruang.edit', [
            'ruang' => $ruang
        ]);
    }
    public function update(Request $request, Ruang $ruang)
    {
        $request->validate([
            'kode_ruang' => 'required|max:10|alpha_num|unique:ruangs,kode_ruang,' . $ruang->id,
            'nama_ruang' => 'required|max:255',
            'keterangan' => 'nullable|max:255'
        ]);

        $request->merge([
            'kode_ruang' => strtoupper(strtolower($request->kode_ruang))
        ]);

        $ruang->update($request->all());

        return redirect()->route('ruang.index')
            ->with('message', 'success update');
    }
    public function destroy(Ruang $ruang)
    {
        $ruang->delete();

        return back()->with('message', 'success delete');
    }
}
