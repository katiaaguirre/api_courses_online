<?php

namespace App\Http\Resources\Repertorio\Opciones;

use Illuminate\Http\Resources\Json\JsonResource;

class RepertorioOpcionResource extends JsonResource
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
            "cancion_id" => $this->resource->cancion_id,
            "tonalidad" => $this->resource->tonalidad,
            "video" => $this->resource->video,
            "count_files" => $this->resource->files->count(),
            "files" => [
                "audio" => $this->getFileByType(0),
                "armonias" => $this->getFileByType(1),
                "cuerdas" => $this->getFileByType(2),
                "metales" => $this->getFileByType(3),
                "bajo" => $this->getFileByType(4),
            ],
        ];
    }

    /**
     * Get file by type.
     *
     * @param int $type
     * @return array|null
     */
    private function getFileByType($type)
    {
        $file = $this->resource->files->where('tipo', $type)->first();
        
        if ($file) {
            return [
                "id" => $file->id,
                "tipo" => $file->tipo,
                "name" => $file->name_file,
                "type" => $file->type,
                "size" => $file->size
            ];
        }

        return null;
    }
}
