<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Transaction::all();
        return view( 'transaction.data', [ 'data'=>$data ] );
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
        $store = new Transaction();
        $store->information = $request->input('information');
        $store->image = $request->input('image');
        $store->price = $request->input('price');
        $store->date = $request->input('date');
        $store->condition = $request->input('condition');
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
    public function edit(Transaction $transaction)
    {
        //
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
