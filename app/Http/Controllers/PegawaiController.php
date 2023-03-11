<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $pegawais = Pegawai::when($search, function ($query, $search) {
            return $query->where('nama_pegawai', 'like', "%{ $search}%")
                ->orWhere('username', 'like', "%{$search}%");
        })
            ->paginate();
        if ($search) {
            $pegawais->appends(['search' => $search]);
        }
        return view('pegawai.index', [
            'pegawais' => $pegawais,
        ]);
    }
    public function create()
    {
        return view('pegawai.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_pegawai' => 'required|between:3,255',
            'username' => 'required|alpha_dash|between:3,255|unique:petugas',
            'password' => 'required|min:4|confirmed',
            'alamat' => 'required|between:10,500',
            'nip' => 'nullable|between:18,255'
        ]);

        $request->merge([
            'password' => bcrypt($request->password),
        ]);

        Pegawai::create($request->all());

        return redirect()->route('pegawai.index')
            ->with('message', 'success store');
    }
    public function show()
    {
        return abort(404);
    }
    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', [
            'pegawai' => $pegawai
        ]);
    }
    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama_pegawai' => 'required|between:3,255',
            'username' => 'required|between:3,255|unique:pegawais,username,' . $pegawai->id,
            'password' => 'nullable|between:4,255|confirmed',
            'alamat' => 'required|between:10,500',
            'nip' => 'nullable|between:18,255'
        ]);

        if($request->password){
            $request->merge([
                'password' => bcrypt($request->password),
            ]);
            $pegawai->update($request->all());
        } else {
            $pegawai->update($request->except('password'));
        }

        return redirect()->route('pegawai.index')
            ->with('message', 'success update');
    }
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return back()->with('message', 'success delete');
    }
}
