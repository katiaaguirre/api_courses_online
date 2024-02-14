<?php

namespace App\Models\Course;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount\DiscountCategory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "name",
        "image",
        "category_id",
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

   public function children(){
    return $this->hasMany(Category::class, "category_id");
   }

   public function father(){
    return $this->belongsTo(Category::class, "category_id");
   }

   public function courses(){
    return $this->hasMany(Course::class);
   }

   public function discount_categories(){
    return $this->hasMany(DiscountCategory::class);
}

   function scopeFilterAdvance($query, $search, $state){
    if($search){
        $query->where("name","like","%".$search."%");
    }
    if($state){
        $query->where("state", $state);
    }

    return $query;
    }
}
