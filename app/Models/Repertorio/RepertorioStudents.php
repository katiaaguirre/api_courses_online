<?php

namespace App\Models\Repertorio;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepertorioStudents extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "repertorio_id",
        "user_id",
        "instrumento"
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

    public function user(){
        return $this->belongsTo(User::class);
    }
}
