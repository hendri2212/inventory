<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Borrow::all();
        return view( 'Borrow.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $data = Borrow::all();
        return view( 'Borrow.Add', [ 'data'=>$data ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrow $borrow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Borrow $borrow){
        return view( 'Borrow.Edit', [ 
            'borrow' => $borrow,
            $data = Borrow::all()
        ]);
        return redirect('/borrow');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Borrow $borrow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Borrow $borrow){
        Borrow::destroy($borrow->id);
        return redirect('/borrow')->with('Data berhasil dihapus');
    }
}
