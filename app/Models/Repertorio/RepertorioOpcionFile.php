<?php

namespace App\Models\Repertorio;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepertorioOpcionFile extends Model
{
    use HasFactory;
    protected $table = "repertorio_opcion_file";
    protected $fillable = [
        "id",
        "opcion_id",
        "name_file",
        "file",
        "type",
        "tipo",
        "size"
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["created_at"] = Carbon::now();
       }
    
    public function setUpdatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["updated_at"] = Carbon::now();
    } 

    public function opcion(){
        return $this->belongsTo(RepertorioOpcion::class);
    }
}
