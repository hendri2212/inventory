<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Good;
use App\Models\Supplier;
use App\Models\Room;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Transaction::all();
        return view( 'Transaction.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $good = Good::all();
        $supplier = Supplier::all();
        $room = Room::all();
        return view( 'Transaction.Add', [ 'good'=>$good, 'supplier'=>$supplier, 'room'=>$room ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $store = new Transaction();
        $store->information = $request->input('information');
        $store->image = $request->input('image');
        $store->price = $request->input('price');
        $store->date = $request->input('date');
        $store->condition = $request->input('condition');
        $store->goods_id = $request->input('goods_id');
        $store->shop_id = $request->input('shop_id');
        $store->room_id = $request->input('room_id');
        $store->save();
        return redirect('/transaction');
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
        return view( 'transaction.Edit', [ 
            'transaction' => $transaction,
            $data = transaction::all()
        ]);
        return redirect('/transaction');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction){
        Transaction::destroy($transaction->id);
        return redirect('/transaction')->with('Data berhasil dihapus');
    }
}
