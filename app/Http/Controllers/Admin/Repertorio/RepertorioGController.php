<?php

namespace App\Http\Controllers\Admin\Repertorio;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Http\Controllers\Controller;
use App\Models\Repertorio\Repertorio;
use App\Http\Resources\Repertorio\RepertorioGResource;
use App\Http\Resources\Repertorio\RepertorioGCollection;

class RepertorioGController extends Controller
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
        
        $repertorios = Repertorio::orderby("id", "asc")->get();
        return response()->json([
            "repertorios" => RepertorioGCollection::make($repertorios)
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
        $exists = Repertorio::where("name", $request->name)->first();
        if($exists){
            return response()->json(["message" => 403, "message_text" => "YA EXISTE UN REPERTORIO CON ESTE TÍTULO"]);
        }
        $request->request->add(["slug" => Str::slug($request->name)]);
        $repertorio = Repertorio::create($request->all());

        return response()->json(["repertorio" => RepertorioGResource::make($repertorio)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $repertorio = Repertorio::findOrFail($id);

        return response()->json([
            "repertorio" => RepertorioGResource::make($repertorio)
        ]);
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
        $exists = Repertorio::where("id","<>", $id)->where("name", $request->name)->first();
        if($exists){
            return response()->json(["message" => 403, "message_text" => "YA EXISTE UN REPERTORIO CON ESTE TÍTULO"]);
        }
        $repertorio = Repertorio::findOrFail($id);
        $request->request->add(["slug" => Str::slug($request->name)]);
        $repertorio->update($request->all());
        
        return response()->json(["repertorio" => RepertorioGResource::make($repertorio)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $repertorio = Repertorio::findOrFail($id);
        $repertorio->delete();
        return response()->json(["message" => 200]);
    }
}
