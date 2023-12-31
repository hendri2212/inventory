<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Good;
use App\Models\Room;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Borrow::with('Good','Room')->get();
        return view( 'Borrow.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $borrow = Borrow::all();
        $good = Good::all();
        $room = Room::all();
        return view( 'Borrow.Add', [ 'borrow'=>$borrow, 'good'=>$good, 'room'=>$room ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){

        $rules = [
            'name' => 'required',
            'good_id' => 'required',
            'room_id' => 'required'
        ];

        $validatedData = $request->validate($rules);

        Borrow::create($validatedData);
            
        return redirect('/borrow')->with('Data berhasil diupdate');
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
        $borrow = Borrow::all();
        $good = Good::all();
        $room = Room::all();
        return view( 'Borrow.Edit', [ 'borrow'=>$borrow, 'good'=>$good, 'room'=>$room ] );
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
