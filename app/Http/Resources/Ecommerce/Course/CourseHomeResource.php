<?php

namespace App\Http\Resources\Ecommerce\Course;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
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
            "slug" => $this->resource->slug,
            "subtitle" => $this->resource->subtitle,
            "image" => env("APP_URL")."storage/".$this->resource->image,
            "precio_usd" => $this->resource->precio_usd,
            "precio_mxn" => $this->resource->precio_mxn,
            "count_class" => $this->resource->count_class,
            "course_time" => $this->resource->course_time,
            "discount_g" => $discount_g,
            "count_students" => $this->resource->count_students,
            "avg_review" => $this->resource->avg_review ? round($this->resource->avg_review,2) : 0,
            "count_reviews" => $this->resource->count_reviews,
            "instructor" => $this->resource->instructor ? [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name. ' '. $this->resource->instructor->surname,
                "avatar" => env("APP_URL")."storage/".$this->resource->instructor->avatar,
                "slug" => $this->resource->instructor->slug
            ] : NULL
        ];
    }
}
