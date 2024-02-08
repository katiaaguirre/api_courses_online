<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\Course\CourseClase;
use App\Models\Course\CourseSection;
use App\Models\Course\CourseClaseFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseClase extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "course_section_id",
        "name",
        "description",
        "url_video",
        "time",
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
   
   public function course_section(){
    return $this->belongsTo(CourseSection::class);
   }

   public function files(){
    return $this->hasMany(CourseClaseFile::class, "course_clase_id");
   }
   
}
