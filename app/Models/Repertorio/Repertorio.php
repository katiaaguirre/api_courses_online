<?php

namespace App\Models\Repertorio;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Repertorio extends Model
{
    use HasFactory;
    protected $table = "repertorio";
    protected $fillable = [
        "id",
        "name",
        "slug",
        "subtitle",
        "description",
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

    public function canciones(){
        return $this->hasMany(RepertorioCancion::class);
    }

    public function repertorio_students(){
        return $this->hasMany(RepertorioStudents::class);
       }
}
