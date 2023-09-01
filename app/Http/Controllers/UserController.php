<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = User::all();
        return view( 'User.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $data = Company::all();
        return view( 'User.Add', [ 'data'=>$data ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $store = new User();
        $store->name = $request->input('name');
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
        return view( 'user.Edit', [ 
            'user' => $user,
            $data = user::all()
        ]);
        return redirect('/user');
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
