<?php

namespace App\Models\Repertorio;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepertorioCancion extends Model
{
    use HasFactory;
    protected $table = "repertorio_cancion";
    protected $fillable = [
        "id",
        "repertorio_id",
        "name",
        "state"
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["created_at"] = Carbon::now();
       } 
    
    public function setUpdatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function repertorio(){
        return $this->belongsTo(Repertorio::class);
    }

    public function opciones(){
        return $this->hasMany(RepertorioOpcion::class,"cancion_id");
    }
}
