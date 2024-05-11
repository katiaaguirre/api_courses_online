<?php

namespace App\Http\Controllers\Admin\Repertorio;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Repertorio\RepertorioOpcion;
use App\Models\Repertorio\RepertorioOpcionFile;
use App\Http\Resources\Repertorio\Opciones\RepertorioOpcionResource;
use App\Http\Resources\Repertorio\Opciones\RepertorioOpcionCollection;


class OpcionGController extends Controller
{
    public function index(Request $request)
    {
        $opciones = RepertorioOpcion::where("cancion_id", $request->cancion_id)->orderBy("id", "asc")->get();
        $count_opciones = RepertorioOpcion::where("cancion_id", $request->cancion_id)->count();

        return response()->json([
            "opciones" => RepertorioOpcionCollection::make($opciones),
            "count_opciones" => $count_opciones
        ]);
    }

    public function store(Request $request)
    {
        $opcion = RepertorioOpcion::create($request->all());
        $tipo = null;
        
        if($request->hasFile('audio')){
            $audio = $request->file('audio');
            $extension = $audio->getClientOriginalExtension();
            $name_file = $audio->getClientOriginalName();
            $size = $audio->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $audio);
            $tipo = 0; 
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('armonias')){
            $armonias = $request->file('armonias');
            $extension = $armonias->getClientOriginalExtension();
            $name_file = $armonias->getClientOriginalName();
            $size = $armonias->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $armonias);
            $tipo = 1;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('cuerdas')){
            $cuerdas = $request->file('cuerdas');
            $extension = $cuerdas->getClientOriginalExtension();
            $name_file = $cuerdas->getClientOriginalName();
            $size = $cuerdas->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $cuerdas);
            $tipo = 2;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('metales')){
            $metales = $request->file('metales');
            $extension = $metales->getClientOriginalExtension();
            $name_file = $metales->getClientOriginalName();
            $size = $metales->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $metales);
            $tipo = 3;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('bajo')){
            $bajo = $request->file('bajo');
            $extension = $bajo->getClientOriginalExtension();
            $name_file = $bajo->getClientOriginalName();
            $size = $bajo->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $bajo);
            $tipo = 4;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }    

        return response()->json(["opcion" => RepertorioOpcionResource::make($opcion)]);
    }

    public function update(Request $request, $id)
    {
        $opcion = RepertorioOpcion::findOrFail($id);
        $opcion->update($request->all());
        return response()->json(["opcion" => RepertorioOpcionResource::make($opcion)]);
    }

    public function AddFiles(Request $request)
    {
        $opcion = RepertorioOpcion::findOrFail($request->opcion_id);
        $tipo = null;
        $ya_existe = null;
        
        if($request->hasFile('audio')){
            $ya_existe = RepertorioOpcionFile::where("tipo", 0)->where("opcion_id", $opcion->id)->get();
            
            if(!$ya_existe->isEmpty()){
                foreach ($ya_existe as $file) {
                    Storage::delete($file->file);
                    $file->delete();
                }
            }
            $audio = $request->file('audio');
            $extension = $audio->getClientOriginalExtension();
            $name_file = $audio->getClientOriginalName();
            $size = $audio->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $audio);
            $tipo = 0; 
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('armonias')){
            $ya_existe = RepertorioOpcionFile::where("tipo", 1)->where("opcion_id", $opcion->id)->get();
            
            if(!$ya_existe->isEmpty()){
                foreach ($ya_existe as $file) {
                    Storage::delete($file->file);
                    $file->delete();
                }
            }

            $armonias = $request->file('armonias');
            $extension = $armonias->getClientOriginalExtension();
            $name_file = $armonias->getClientOriginalName();
            $size = $armonias->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $armonias);
            $tipo = 1;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('cuerdas')){
            $ya_existe = RepertorioOpcionFile::where("tipo", 2)->where("opcion_id", $opcion->id)->get();
            
            if(!$ya_existe->isEmpty()){
                foreach ($ya_existe as $file) {
                    Storage::delete($file->file);
                    $file->delete();
                }
            }
            
            $cuerdas = $request->file('cuerdas');
            $extension = $cuerdas->getClientOriginalExtension();
            $name_file = $cuerdas->getClientOriginalName();
            $size = $cuerdas->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $cuerdas);
            $tipo = 2;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('metales')){
            $ya_existe = RepertorioOpcionFile::where("tipo", 3)->where("opcion_id", $opcion->id)->get();
            
            if(!$ya_existe->isEmpty()){
                foreach ($ya_existe as $file) {
                    Storage::delete($file->file);
                    $file->delete();
                }
            }
            $metales = $request->file('metales');
            $extension = $metales->getClientOriginalExtension();
            $name_file = $metales->getClientOriginalName();
            $size = $metales->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $metales);
            $tipo = 3;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }

        if($request->hasFile('bajo')){
            $ya_existe = RepertorioOpcionFile::where("tipo", 4)->where("opcion_id", $opcion->id)->get();
            
            if(!$ya_existe->isEmpty()){
                foreach ($ya_existe as $file) {
                    Storage::delete($file->file);
                    $file->delete();
                }
            }
            $bajo = $request->file('bajo');
            $extension = $bajo->getClientOriginalExtension();
            $name_file = $bajo->getClientOriginalName();
            $size = $bajo->getSize();
            $path = Storage::putFile("repertorio_opciones_files", $bajo);
            $tipo = 4;
            $opcion_file = RepertorioOpcionFile::create([
                "opcion_id" => $opcion->id,
                "name_file" => $name_file,
                "tipo" => $tipo,
                "file" => $path,
                "type" => $extension,
                "size" => $size
            ]);
        }    

        return response()->json(["opcion" => RepertorioOpcionResource::make($opcion->load('files'))]);
    }


    public function RemoveFile($id)
    {
        $opcionFile = RepertorioOpcionFile::findOrFail($id);
        Storage::delete($opcionFile->file);
        $opcionFile->delete();
    
        return response()->json(["message" => 200]);
    }
        


    public function destroy($id)
    {
        $opcion = RepertorioOpcion::findOrFail($id);

        $opcionFiles = RepertorioOpcionFile::where('opcion_id', $opcion->id)->get();
        foreach ($opcionFiles as $opcionFile) {
            Storage::delete($opcionFile->file);
            $opcionFile->delete();
        }

        $opcion->delete();

        return response()->json(["message" => 200]);
    }


}
