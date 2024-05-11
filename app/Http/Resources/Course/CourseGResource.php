<?php

namespace App\Http\Resources\Course;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseGResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "subtitle" => $this->resource->subtitle,
            "slug" => $this->resource->slug,
            "image" => env("APP_URL")."storage/".$this->resource->image,
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
            "precio_usd" => $this->resource->precio_usd,
            "precio_mxn" => $this->resource->precio_mxn,
            "user_id" => $this->resource->user_id,
            "user" => [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name.' '.$this->resource->instructor->surname,
                "email" => $this->resource->instructor->email,
                "slug" => $this->resource->instructor->slug
            ],            
            "level" => $this->resource->level,
            "idioma" => $this->resource->idioma,
            "url_video" => $this->resource->url_video,
            "time" => $this->resource->time,
            "description" => $this->resource->description,
            "requirements" => json_decode($this->resource->requirements),
            "who_is_it_for" => json_decode($this->resource->who_is_it_for),
            "state" => $this->resource->state
        ];
    }
}
