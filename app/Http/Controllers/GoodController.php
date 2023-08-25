<?php

namespace App\Http\Controllers;

use App\Models\Good;
use Illuminate\Http\Request;

class GoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Good::all();
        return view( 'good.data', [ 'data'=>$data ] );
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
        $store = new Good();
        $store->name = $request->input('nama');
        $store->save();
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Good $good){
        return Category::find($good->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Good $good)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Good $good)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Good $good){
        Good::destroy($good->id);
        return redirect('/good')->with('Data berhasil dihapus');
    }
}
