<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\Course\Course;
use App\Models\Course\CourseClase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseSection extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "name",
        "course_id",
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

   public function course(){
    return $this->belongsTo(Course::class);
   }

   public function clases(){
    return $this->hasMany(CourseClase::class, "course_section_id");
   }
}
