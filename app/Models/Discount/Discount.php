<?php

namespace App\Models\Discount;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "code",
        "type_discount",  // 1 es % y 2 es monto fijo
        "discount", // monto de descuento
        "start_date",
        "end_date",
        "discount_type",
        "type_campaign",
        "num_use", // el numero de usos permitidos
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

    public function courses(){
        return $this->hasMany(DiscountCourse::class);
    }

    public function categories(){
        return $this->hasMany(DiscountCategory::class);
    }

    function scopeFilterAdvance($query, $state){
        if($state){
            $query->where("state", $state);
        }
    
        return $query;
    }
}
