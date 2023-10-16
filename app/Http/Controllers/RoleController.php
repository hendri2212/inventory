<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Role::all();
        return view( 'Role.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $data = Role::all();
        return view( 'Role.Add', [ 'data'=>$data ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        // $store = new Role();
        // $store->name = $request->input('name');
        // $store->save();
        // return redirect('/role');

        $rules = [
            'name' => 'required'
        ];

        $validatedData = $request->validate($rules);

        Role::create($validatedData);
            
        return redirect('/role')->with('Data berhasil diupdate');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role){
        return view( 'Role.Edit', [ 
            'role' => $role,
            $data = Role::all()
        ]);
        return redirect('/role');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role){
        Role::destroy($role->id);
        return redirect('/role')->with('Data berhasil dihapus');
    }
}
