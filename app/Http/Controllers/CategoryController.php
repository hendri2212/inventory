<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $data = Category::all();
        return view( 'Category.Data', [ 'data'=>$data ] );
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
    public function store(Request $request) {
        $store = new Category();
        $store->name = $request->input('nama');
        $store->save();
        return redirect('/category');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category) {
        return Category::find($category->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category){
        Category::destroy($category->id);
        return redirect('/category')->with('Data berhasil dihapus');
    }
}
