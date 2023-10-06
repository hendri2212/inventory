<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\Dashboard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $good = Good::count();
        $transaction = Transaction::count();
        $supplier = Supplier::count();
        $room = Room::count();

        return view('Beranda', compact('good', 'transaction', 'supplier','room'));
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
    public function show(Dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dashboard $dashboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dashboard $dashboard)
    {
        //
    }
}
