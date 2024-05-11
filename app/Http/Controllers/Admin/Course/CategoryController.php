<?php

namespace App\Http\Controllers\Admin\Course;

use Illuminate\Http\Request;
use App\Models\Course\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Course\Category\CategoryResource;
use App\Http\Resources\Course\Category\CategoryCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $state = $request->state;
        
        $categories = Category::filterAdvance($search, $state)->orderby("id", "desc")->get();
        return response()->json([
            "categories" => CategoryCollection::make($categories)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile("portada")){
            $path = Storage::putFile("categories", $request->file("portada"));
            $request->request->add(["image" => $path]);
        }
        $category = Category::create($request->all());

        return response()->json(["category" => CategoryResource::make($category)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        if($request->hasFile("portada")){
            if($category->image){
               Storage::delete($category->image);
            }
            $path = Storage::putFile("categories", $request->file("portada"));
            $request->request->add(["image" => $path]);
        }
        $category->update($request->all());
        return response()->json(["category" => CategoryResource::make($category)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if($category->image){
            Storage::delete($category->image);
        }
        $category->delete();
        return response()->json(["message" => 200]);
    }
}