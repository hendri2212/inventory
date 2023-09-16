<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = User::with('Role','Company')->get();
        return view( 'User.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $company = Company::all();
        $role = Role::all();
        return view( 'User.Add', [ 'company'=>$company,'role'=>$role ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $store = new User();
        $store->name = $request->input('name');
        $store->role_id = $request->input('role_id');
        $store->username = $request->input('username');
        $store->password = $request->input('password');
        $store->company_id = $request->input('company_id');
        $store->save();
        return redirect('/user');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user){
        $company = Company::all();
        $role = Role::all();
        return view( 'User.Edit', [ 'user'=>$user,'company'=>$company,'role'=>$role ] );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user){
        User::destroy($user->id);
        return redirect('/user')->with('Data berhasil dihapus');
    }
}
