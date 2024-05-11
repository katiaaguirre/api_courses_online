<?php

namespace App\Models\Repertorio;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Repertorio\RepertorioOpcion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepertorioOpcion extends Model
{
    use HasFactory;
    protected $table = "repertorio_opcion";
    protected $fillable = [
        "id",
        "cancion_id",
        "tonalidad",
        "video",
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["created_at"] = Carbon::now();
       }
    
    public function setUpdatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function cancion(){
        return $this->belongsTo(RepertorioCancion::class);
    } 

    public function files(){
        return $this->hasMany(RepertorioOpcionFile::class,"opcion_id");
    }
}