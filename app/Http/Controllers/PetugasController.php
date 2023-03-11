<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;
use Auth;
class PetugasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $id = Auth::guard('admin')->id();
        $petugas = Petugas::WhereNotIn('id', [$id])
            ->When($search, function ($query, $search) {
                return $query->where('nama_petugas', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}");
            })
            ->paginate();

        $petugas->map(function ($row) {
            $row->level = $row->level == 'admin' ? 'Administrator' : 'Operator';
            return $row;
        });

        if ($search) {
            $petugas->appends(['search' => $search]);
        }

        return view('petugas.index', [
            'petugas' => $petugas,
        ]);
    }

    public function create()
    {
        return view('petugas.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'nama_petugas' => 'required|between:3,255',
            'username' => 'required|alpha_dash|between:3,255|unique:petugas',
            'password' => 'required|min:4|confirmed',
            'level' => 'required|in:admin,operator',
        ]);

        $request->merge([
            'password' => bcrypt($request->password),
        ]);

        Petugas::create($request->all());

        return redirect()->route('petugas.index')
            ->with('message', 'success store');
    }

   
    public function show()
    {
        return abort(404);
    }

    
    public function edit(Petugas $petuga)
    {
        return view('petugas.edit', [
            'petugas' => $petuga
        ]);
    }

   
    public function update(Request $request, Petugas $petuga)
    {
        $request->validate([
            'nama_petugas' => 'required|between:3,255',
            'username' => 'required|between:3,255|unique:petugas,username,' . $petuga->id,
            'password' => 'nullable|min:4|confirmed',
            'level' => 'required|in:admin,operator',
        ]);

        if($request->password) {
            $request->merge([
                'password' => bcrypt($request->password),
            ]);
            $petuga->update($request->all());
        } else {
            $petuga->update($request->except('password'));
        }

        return redirect()->route('petugas.index')
            ->with('message', 'success update');
    }

   
    public function destroy(Petugas $petuga)
    {
        $petuga->delete();

        return back()->with('message', 'success delete');
    }
}
