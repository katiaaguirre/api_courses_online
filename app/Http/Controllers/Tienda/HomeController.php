<?php

namespace App\Http\Controllers\Tienda;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Course\Category;
use App\Models\Discount\Discount;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ecommerce\Course\CourseHomeResource;
use App\Http\Resources\Ecommerce\Course\CourseHomeCollection;

class HomeController extends Controller
{
    public function home(Request $request){
        $categories = Category::where("category_id", NULL)->withCount("courses")->orderBy("id", "desc")->get();
        $courses = Course::where("state",2)->inRandomOrder()->limit(3)->get();
        
        $category_courses = Category::where("category_id", NULL)->withCount("courses")
        ->having("courses_count",">",0)->orderBy("id", "desc")->take(5)->get();
        
        $group_category_courses = collect([]);
        foreach($category_courses as $key => $category_course){
            $group_category_courses->push([
                "id" => $category_course->id,
                "name" => $category_course->name,
                "name_empty" => str_replace(" ", "", $category_course->name),
                "courses_count" => $category_course->courses_count,
                "courses" => CourseHomeCollection::make($category_course->courses)
            ]);
        }

        date_default_timezone_set("America/Mexico_city");
        $DISCOUNT_BANNER = Discount::where("type_campaign",3)->where("state",1)
        ->where("start_date","<=",today())->where("end_date",">=",today())
        ->first();

        $DISCOUNT_BANNER_COURSES = collect([]);
        if($DISCOUNT_BANNER){
            foreach($DISCOUNT_BANNER->courses as $key => $course_discount){
                $DISCOUNT_BANNER_COURSES->push(CourseHomeResource::make($course_discount->course));
            }
        }

        date_default_timezone_set("America/Mexico_city");
        $DISCOUNT_FLASH = Discount::where("type_campaign",2)->where("state",1)
        ->where("start_date","<=",today())->where("end_date",">=",today())
        ->first();

        $DISCOUNT_FLASH_COURSES = collect([]);
        if($DISCOUNT_FLASH){
            foreach($DISCOUNT_FLASH->courses as $key => $course_discount){
                $DISCOUNT_FLASH_COURSES->push(CourseHomeResource::make($course_discount->course));
            }
        }

        return response()->json([
            "categories" => $categories->map(function($category){
                return [
                    "id" => $category->id,
                    "name" => $category->name,
                    "image" => env("APP_URL")."storage/".$category->image,
                    "course_count" => $category->courses_count
                ];
            }),
            "courses_home" => CourseHomeCollection::make($courses),
            "group_category_courses" => $group_category_courses,
            "DISCOUNT_BANNER" => $DISCOUNT_BANNER,
            "DISCOUNT_BANNER_COURSES" => $DISCOUNT_BANNER_COURSES,
            "DISCOUNT_FLASH" => $DISCOUNT_FLASH ? [
                "id" => $DISCOUNT_FLASH->id,
                "discount" => $DISCOUNT_FLASH->discount,
                "type_discount" => $DISCOUNT_FLASH->type_discount,
                "end_date" => Carbon::parse($DISCOUNT_FLASH->end_date)->format("Y-m-d"),
                "start_date_d" => Carbon::parse($DISCOUNT_FLASH->start_date)->format("Y/m/d"),
                "end_date_d" => Carbon::parse($DISCOUNT_FLASH->end_date)->format("Y/m/d")
            ] : NULL,
            "DISCOUNT_FLASH_COURSES" => $DISCOUNT_FLASH_COURSES
        ]);
    }
}
