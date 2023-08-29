<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Supplier::all();
        return view( 'supplier.data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $store = new Supplier();
        $store->name = $request->input('name');
        $store->owner = $request->input('owner');
        $store->telephone = $request->input('telephone');
        $store->addres = $request->input('addres');
        $store->save();
        return redirect('/supplier');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier){
        Supplier::destroy($supplier->id);
        return redirect('/supplier')->with('Data berhasil dihapus');
    }
}
