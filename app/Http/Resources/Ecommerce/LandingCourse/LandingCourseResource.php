<?php

namespace App\Http\Resources\Ecommerce\LandingCourse;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingCourseResource extends JsonResource
{
    public function toArray($request)
    {
        $discount_g = null;
        if($this->resource->discount_c && $this->resource->discount_c_t){
            $discount_g = $this->resource->discount_c_t;
        }else{
            if($this->resource->discount_c && !$this->resource->discount_c_t){
                $discount_g = $this->resource->discount_c;
            }else{
                if(!$this->resource->discount_c && $this->resource->discount_c_t){
                    $discount_g = $this->resource->discount_c_t;
                }
            }
        }
        
        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "subtitle" => $this->resource->subtitle,
            "slug" => $this->resource->slug,
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
            "count_students" => $this->resource->count_students,
            "count_reviews" => $this->resource->count_reviews,
            "avg_review" => $this->resource->avg_review ? round($this->resource->avg_review,2) : 0,
            "discount_g" => $discount_g,
            "discount_date" => $discount_g ? "el ".Carbon::parse($discount_g->end_date)->format("d/m") : "pronto",
            "description" => $this->resource->description,
            "requirements" => json_decode($this->resource->requirements),
            "who_is_it_for" => json_decode($this->resource->who_is_it_for),
            "instructor" => $this->resource->instructor ? [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name. ' '. $this->resource->instructor->surname,
                "avatar" => env("APP_URL")."storage/".$this->resource->instructor->avatar,
                "slug" => $this->resource->instructor->slug,
                "profesion" => $this->resource->instructor->profesion,
                "description" => $this->resource->instructor->description,
                "avg_review" => round($this->resource->instructor->avg_review,2),
                "count_reviews" => $this->resource->instructor->count_reviews,
                "count_courses" => $this->resource->instructor->count_courses,
                "count_students" => $this->resource->instructor->count_students,
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
                            "clase_time" => $clase->clase_time,
                            "url_video" => $clase->url_video,
                            "files" => $clase->files->map(function($file){
                                return [
                                    "name" => $file->name_file,
                                    "url" => env("APP_URL")."storage/".$file->file,
                                    "size" => $file->size
                                ];
                            })
                        ];
                    }),
                ];
            }),
            "updated_at" => $this->resource->updated_at->format("m/Y"),
            "reviews" => $this->resource->reviews->map(function($review){
                return [
                    "message" => $review->message,
                    "rating" => $review->rating,
                    "user" => [
                        "full_name" => $review->user->name.' '.$review->user->surname,
                        "avatar" => env("APP_URL")."storage/".$review->user->avatar
                    ]   
                ];
            })
        ];
    }
}
