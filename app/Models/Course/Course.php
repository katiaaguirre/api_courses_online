<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course\CourseSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "title",
        "subtitle",
        "slug",
        "image",
        "precio_usd",
        "precio_mxn",
        "category_id",
        "sub_category_id",
        "user_id",
        "level",
        "idioma",
        "url_video",
        "time",
        "description",
        "requirements",
        "who_is_it_for",
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

   public function instructor(){
    return $this->belongsTo(User::class, 'user_id');
   }

   public function category(){
    return $this->belongsTo(Category::class);
   }

   public function sub_category(){
    return $this->belongsTo(Category::class);
   }

   public function sections(){
    return $this->belongsTo(CourseSection::class);
   }

   function scopeFilterAdvance($query, $search, $state){
    if($search){
        $query->where("title","like","%".$search."%");
    }
    if($state){
        $query->where("state", $state);
    }

    return $query;
}
}
