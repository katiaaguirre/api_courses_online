<?php

namespace App\Http\Controllers\Tienda;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Repertorio\Repertorio;
use App\Models\Repertorio\RepertorioStudents;
use App\Http\Resources\Repertorio\RepertorioResource;

class RepertorioController extends Controller
{
    public function repertorio(Request $request, $slug)
    {
        $user = auth('api')->user();

        $repertorio = Repertorio::where("slug", $slug)->where("state",2)->first();
        
        if (!$repertorio) {
            return response()->json(["message" => 403, "message_text" => "El repertorio no existe"]);
        }
        
        $repertorio_student = RepertorioStudents::where("user_id", $user->id)
            ->where("repertorio_id", $repertorio->id)
            ->first();
    
        if (!$repertorio_student) {
            return response()->json(["message" => 403, "message_text" => "No tienes acceso a este repertorio"]);
        }


        $instrumento = $repertorio_student->instrumento;

        if (!is_array($instrumento)) {
            $instrumento = json_decode($instrumento, true);
        }
    
        $allowedTypes = json_decode($repertorio_student->instrumento, true);

        $repertorio->canciones->each(function ($cancion) use ($allowedTypes) {
            $cancion->opciones->each(function ($opcion) use ($allowedTypes) {
                $opcion->files = $opcion->files->filter(function ($file) use ($allowedTypes) {
                    return $file->tipo == 0 || in_array($file->tipo, $allowedTypes);
                });
            });
        });

        return response()->json([
            "repertorio" => RepertorioResource::make($repertorio),
        ]);
    }
    
}
