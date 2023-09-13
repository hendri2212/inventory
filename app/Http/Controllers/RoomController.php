<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Company;
use App\Models\Good;
use App\Models\Supplier;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Room::with('Company','Good','Supplier')->get();
        return view( 'Room.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $company = Company::all();
        $good = Good::all();
        $supplier = Supplier::all();
        return view( 'Room.Add', [ 'company'=>$company, 'good'=>$good, 'supplier'=>$supplier ] );
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
        // $room = Room::with('company')->find($room->id);
        $company = Company::all();
        $good = Good::all();
        $supplier = Supplier::all();
        return view( 'Room.Edit', [ 'company'=>$company, 'good'=>$good, 'supplier'=>$supplier ] );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        
        $rules = [
            'name'=> 'required',
            'company_id' => 'required'
        ];

        $validatedData = $request->validate($rules);

        Room::where('id', $room->id)
            ->update($validatedData);
            
        
        return redirect('/room')->with('Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room){
        Room::destroy($room->id);
        return redirect('/room')->with('Data berhasil dihapus');
    }
}
