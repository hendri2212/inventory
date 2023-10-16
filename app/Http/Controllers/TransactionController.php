<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Good;
use App\Models\Supplier;
use App\Models\Room;
use App\Models\Borrow;
use App\Models\Condition;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Transaction::with('Room','Good','Supplier','Borrow','Condition')->get();
        return view( 'Transaction.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $good = Good::all();
        $supplier = Supplier::all();
        $room = Room::all();
        $borrow = Borrow::all();
        $condition = Condition::all();
        return view( 'Transaction.Add', [ 'good'=>$good, 'supplier'=>$supplier, 'room'=>$room, 'borrow'=>$borrow, 'condition'=>$condition ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        // $store = new Transaction();
        // $store->information = $request->input('information');
        // $store->image = $request->input('image');
        // $store->price = $request->input('price');
        // $store->date = $request->input('date');
        // $store->borrow_id = $request->input('borrow_id');
        // $store->condition_id = $request->input('condition_id');
        // $store->good_id = $request->input('good_id');
        // $store->supplier_id = $request->input('supplier_id');
        // $store->room_id = $request->input('room_id');
        // $store->save();
        // return redirect('/transaction');

        $rules = [
            'information' => 'required',
            'image' => 'image|file|nullable',
            'price' => 'required',
            'date' => 'required',
            'borrow_id' => 'required',
            'condition_id' => 'required',
            'good_id' => 'required',
            'supplier_id' => 'required',
            'room_id' => 'required'
        ];

        $validatedData = $request->validate($rules);

        if($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('images');
        }

        Transaction::create($validatedData);
            
        // return redirect('/transaction')->with('Data berhasil diupdate');
        return $request;
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction){
        $good = Good::all();
        $supplier = Supplier::all();
        $room = Room::all();
        $borrow = Borrow::all();
        $condition = Condition::all();
        return view( 'Transaction.Edit', [ 'good'=>$good, 'supplier'=>$supplier, 'room'=>$room, 'borrow'=>$borrow, 'condition'=>$condition ] );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction){
        
            $rules = [
                'information'=> 'required',
                'image'=> 'image|file|nullable',
                'price'=> 'required',
                'date'=> 'required',
                'borrow_id'=> 'required',
                'condition_id'=> 'required',
                'good_id'=> 'required',
                'supplier_id'=> 'required',
                'room_id'=> 'required'
            ];
    
            $validatedData = $request->validate($rules);
    
            Transaction::where('id', $transaction->id)
                ->update($validatedData);
                
            
            return redirect('/transaction')->with('Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction){
        Transaction::destroy($transaction->id);
        return redirect('/transaction')->with('Data berhasil dihapus');
    }
}
