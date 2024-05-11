<?php

namespace App\Http\Controllers\Admin\Repertorio;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Repertorio\RepertorioCancion;

class CancionGController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $canciones = RepertorioCancion::withCount("opciones")->where("repertorio_id", $request->repertorio_id)->orderBy("name","asc")->get();
        $count_canciones = RepertorioCancion::where("repertorio_id", $request->repertorio_id)->count();

        return response()->json([
            "canciones" => $canciones,
            "count_canciones" => $count_canciones
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
        $cancion = RepertorioCancion::create($request->all());

        return response()->json(["cancion" => $cancion]);
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
        $cancion = RepertorioCancion::findOrFail($id);
        $cancion->update($request->all());

        return response()->json(["cancion" => $cancion]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cancion = RepertorioCancion::findOrFail($id);

        if($cancion->opciones !== null && $cancion->opciones->count() > 0){
            return response()->json(["message" => 403, "message_text" => "NO PUEDES ELIMINAR ESTA CANCIÓN PORQUE TIENE OPCIONES DENTRO"]);
        }
        $cancion->delete();
        
        return response()->json(["message" => 200]);
    }
}
