<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Company::all();
        return view( 'Company.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $data = Company::all();
        return view( 'Company.Add', [ 'data'=>$data ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $store = new Company();
        $store->name = $request->input('name');
        $store->address = $request->input('address');
        $store->telephone = $request->input('telephone');
        $store->save();
        return redirect('/company');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company){
        Company::destroy($company->id);
        return redirect('/company')->with('Data berhasil dihapus');
    }
}
