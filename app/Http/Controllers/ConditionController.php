<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use Illuminate\Http\Request;

class ConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Condition::all();
        return view( 'Condition.Data', [ 'data'=>$data ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $data = Condition::all();
        return view( 'Condition.Add', [ 'data'=>$data ] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        // $store = new Condition();
        // $store->name = $request->input('name');
        // $store->save();
        // return redirect('/condition');

        $rules = [
            'name' => 'required'
        ];

        $validatedData = $request->validate($rules);

        Condition::create($validatedData);
            
        return redirect('/condition')->with('Data berhasil diupdate');
    }

    /**
     * Display the specified resource.
     */
    public function show(Condition $condition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Condition $condition){
        return view( 'Condition.Edit', [ 
            'condition' => $condition,
            $data = Condition::all()
        ]);
        return redirect('/condition');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Condition $condition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Condition $condition){
        Condition::destroy($condition->id);
        return redirect('/condition')->with('Data berhasil dihapus');
    }
}
