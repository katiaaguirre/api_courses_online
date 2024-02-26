<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale\Review;
use App\Models\CoursesStudents;
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

   public function discount_courses(){
    return $this->hasMany(DiscountCourse::class);
   }

   public function courses_students(){
    return $this->hasMany(CoursesStudents::class);
   }

   public function reviews(){
    return $this->hasMany(Review::class);
   }

   public function GetCountFilesAttribute(){
    $count_files = 0;
    foreach($this->sections as $keyS => $section){
        foreach($section->clases as $keyC => $clase){
            $count_files += $clase->files->count();
        }
    }
    return $count_files;
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

    public function getDiscountCAttribute(){
        date_default_timezone_set("America/Mexico_city");
        $discount = null;
        if($this->discount_courses){
            foreach($this->discount_courses as $key => $discount_course){
                if($discount_course->discount && $discount_course->discount->type_campaign == 1 && $discount_course->discount->state == 1){
                    if(Carbon::now()->between($discount_course->discount->start_date,Carbon::parse($discount_course->discount->end_date)->addDays(1))){
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
        if($this->category->discount_categories){
            foreach($this->category->discount_categories as $key => $discount_category){
                if($discount_category->discount && $discount_category->discount->type_campaign == 1 && $discount_category->discount->state == 1){
                    if(Carbon::now()->between($discount_category->discount->start_date,Carbon::parse($discount_category->discount->end_date)->addDays(1))){
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

   public function getCountStudentsAttribute(){
    return $this->courses_students->count();
   }

   public function getCountReviewsAttribute(){
    return $this->reviews->count();
   }

   public function getAvgReviewAttribute(){
    return $this->reviews->avg("rating");
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

    function scopeFilterAdvanceEcommerce($query, $search, $selected_categories = [], $min_price = 0, $max_price = 0,
    $selected_idiomas = [],$selected_levels = [],$courses_a = [],$selected_rating = 0){
        if($search){
            $query->where("title","like","%".$search."%");
        }
        if(sizeof($selected_categories) > 0){
            $query->whereIn("category_id",$selected_categories);
        }
        if($min_price > 0 && $max_price > 0){
            $query->whereBetween("precio_mxn",[$min_price,$max_price]);
        }
        if(sizeof($selected_idiomas) > 0){
            $query->whereIn("idioma",$selected_idiomas);
        }
        if(sizeof($selected_levels) > 0){
            $query->whereIn("level",$selected_levels);
        }
        if($courses_a || $selected_rating){
            $query->whereIn("id",$courses_a);
        }
        return $query;
    }
}
