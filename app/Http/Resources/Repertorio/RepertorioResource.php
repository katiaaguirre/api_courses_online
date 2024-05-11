<?php

namespace App\Http\Resources\Repertorio;

use Illuminate\Http\Resources\Json\JsonResource;

class RepertorioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "subtitle" => $this->resource->subtitle,
            "description" => $this->resource->description,
            "state" => $this->resource->state,
            "canciones" => $this->resource->canciones->map(function($cancion){
                return [
                    "id" => $cancion->id,
                    "repertorio_id" => $cancion->repertorio_id,
                    "name" => $cancion->name,
                    "state" => $cancion->state,
                    "opciones" => $cancion->opciones->map(function($opcion){
                        return [
                            "id" => $opcion->id,
                            "cancion_id" => $opcion->cancion_id,
                            "tonalidad" => $opcion->tonalidad,
                            "video" => $opcion->video,
                            "files" => $opcion->files->map(function($file){
                                return [
                                    "id" => $file->id,
                                    "opcion_id" => $file->opcion_id,
                                    "name_file" => $file->name_file,
                                    "tipo" => $file->tipo,
                                    "file" => env("APP_URL")."storage/".$file->file,
                                    "type" => $file->type,
                                    "size" => $file->size
                                ];
                            })
                        ];
                    })
                ];
            })
        ];
    }
}
