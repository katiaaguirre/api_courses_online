<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course\CourseSection;
use App\Models\Discount\DiscountCourse;
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
    return $this->hasMany(CourseSection::class);
   }

   function AddTime($horas)
    {
        $total = 0;
        foreach($horas as $h) {
            $parts = explode(":", $h);
            $total += $parts[2] + $parts[1]*60 + $parts[0]*3600;
        }
        $hours = floor($total / 3600);
        $minutes = floor(($total / 60) % 60);
        $seconds = $total % 60;

        return $hours." hrs ".$minutes." mins";
    }

    public function discount_courses(){
        return $this->hasMany(DiscountCourse::class);
    }

    public function getDiscountCAttribute(){
        date_default_timezone_set("America/Mexico_city");
        $discount = null;
        if($this->discount_courses){
            foreach($this->discount_courses as $key => $discount_course){
                if($discount_course->discount && $discount_course->discount->type_campaign == 1 && $discount_course->discount->state == 1){
                    if (Carbon::now()->between($discount_course->discount->start_date, $discount_course->discount->end_date)) {
                        $discount = $discount_course->discount;
                        break;
                    }
                }
            }
            return $discount;
        }
    }
    
    public function getDiscountCTAttribute(){
        $discount = null;
        if($this->category && $this->category->discount_category){
            foreach($this->category->discount_category as $key => $discount_category){
                if($discount_category->discount && $discount_category->discount->type_campaign == 1 && $discount_category->discount->state == 1){
                    if(Carbon::now()->between($discount_category->discount->start_date, $discount_category->discount->end_date)){
                        $discount = $discount_category->discount;
                        break;
                    }
                }
            }
            return $discount;
        }
    }    

   public function getCountClassAttribute(){
    $num = 0;
    foreach ($this->sections as $key => $section) {
        $num += $section->clases->count();
    }
    return $num;
   }

   public function getCourseTimeAttribute(){
        $time = [];
        foreach ($this->sections as $keyS => $section) {
        foreach ($section->clases as $keyC => $clase) {
            array_push($time,$clase->time);
        }
        }
        return $this->AddTime($time);
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
