<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Company;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Supplier::with('Company')->get();
        return view( 'Supplier.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $supplier = Supplier::all();
        $company = Company::all();
        return view( 'Supplier.Add', [ 'supplier'=>$supplier, 'company'=>$company ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        // $store = new Supplier();
        // $store->name = $request->input('name');
        // $store->company_id = $request->input('company_id');
        // $store->image = $request->input('image');
        // $store->telephone = $request->input('telephone');
        // $store->address = $request->input('address');
        // $store->save();
        // return redirect('/supplier');

        $rules = [
            'name' => 'required',
            'company_id' => 'required',
            'image' => 'image|file|nullable',
            'telephone' => 'required',
            'address' => 'required'
        ];

        $validatedData = $request->validate($rules);

        if($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('images');
        }

        Supplier::create($validatedData);
            
        return redirect('/supplier')->with('Data berhasil diupdate');
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
    public function edit(Supplier $supplier){
        // $supplier = Supplier::all();
        $company = Company::all();
        return view( 'Supplier.Edit', [ 'supplier'=>$supplier, 'company'=>$company ] );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
       
        $rules = [
            'name'=> 'required',
            'company_id' => 'required',
            'image' => 'nullable',
            'telephone' => 'required',
            'address' => 'required'
        ];

        $validatedData = $request->validate($rules);

        Supplier::where('id', $supplier->id)
            ->update($validatedData);
            
        
        return redirect('/supplier')->with('Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier){
        Supplier::destroy($supplier->id);
        return redirect('/supplier')->with('Data berhasil dihapus');
    }
}
