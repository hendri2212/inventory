<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Company;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Room::all();
        return view( 'Room.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $data = Company::all();
        return view( 'Room.Add', [ 'data'=>$data ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $store = new Room();
        $store->name = $request->input('name');
        $store->company_id = $request->input('company_id');
        $store->save();
        return redirect('/room');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room){
        return view( 'room.Edit', [ 
            'room' => $room,
            $data = room::all()
        ]);
        return redirect('/room');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room){
        Room::destroy($room->id);
        return redirect('/room')->with('Data berhasil dihapus');
    }
}
