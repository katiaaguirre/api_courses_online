<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesDetails extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "sale_id",
        "course_id", 
        "type_discount",
        "discount",
        "type_campaign",
        "coupon_code",
        "discount_code",
        "precio_unitario",
        "total"
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["created_at"] = Carbon::now();
       }
    
    public function setUpdatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }
    
    public function sale(){
        return $this->belongsTo(Sale::class);
    }

    public function review(){
        return $this->hasOne(Review::class);
    }
}
