<?php

namespace App\Http\Resources\Ecommerce\LandingCourse;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingCourseResource extends JsonResource
{
    public function toArray($request)
    {
        $discount_g = null;
        if($this->resource->discount_c_t && $this->resource->discount_c){
            $discount_g = $this->resource->discount_c_t;
        }else{
            if($this->resource->discount_c_t && !$this->resource->discount_c){
                $discount_g = $this->resource->discount_c_t;
            }else{
                if($this->resource->discount_c_t && !$this->resource->discount_c){
                    $discount_g = $this->resource->discount_c;
                }
            }
        }
        
        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "subtitle" => $this->resource->subtitle,
            "category_id" => $this->resource->category_id,
            "category" => [
                "id" => $this->resource->category->id,
                "name" => $this->resource->category->name
            ],
            "sub_category_id" => $this->resource->sub_category_id,
            "sub_category" => [
                "id" => $this->resource->sub_category->id,
                "name" => $this->resource->sub_category->name
            ],
            "level" => $this->resource->level,
            "idioma" => $this->resource->idioma,
            "url_video" => $this->resource->url_video,
            "time" => $this->resource->time,
            "image" => env("APP_URL")."storage/".$this->resource->image,
            "precio_usd" => $this->resource->precio_usd,
            "precio_mxn" => $this->resource->precio_mxn,
            "count_class" => $this->resource->count_class,
            "course_time" => $this->resource->course_time,
            "count_files" => $this->resource->count_files,
            "discount_g" => $discount_g,
            "discount_date" => $discount_g ? Carbon::parse($discount_g->end_date)->format("d/m") : NULL,
            "description" => $this->resource->description,
            "requirements" => json_decode($this->resource->requirements),
            "who_is_it_for" => json_decode($this->resource->who_is_it_for),
            "instructor" => $this->resource->instructor ? [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name. ' '. $this->resource->instructor->surname,
                "avatar" => env("APP_URL")."storage/".$this->resource->instructor->avatar,
                "profesion" => $this->resource->instructor->profesion,
                "count_courses" => $this->resource->instructor->count_courses,
                "description" => $this->resource->instructor->description
            ] : NULL,
            "malla" => $this->resource->sections->map(function($section){
                return [
                    "id" => $section->id,
                    "name" => $section->name,
                    "section_time" => $section->section_time,
                    "clases" => $section->clases->map(function($clase){
                        return [
                            "id" => $clase->id,
                            "name" => $clase->name,
                            "clase_time" => $clase->clase_time
                        ];
                    }),
                    "updated_at" => $this->resource->updated_at->format("m/Y")
                ];
            })
        ];
    }
}
