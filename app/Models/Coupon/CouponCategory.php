<?php

namespace App\Models\Coupon;

use Carbon\Carbon;
use App\Models\Course\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        "coupon_id",
        "category_id"
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["created_at"] = Carbon::now();
       }
    
    public function setUpdatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
