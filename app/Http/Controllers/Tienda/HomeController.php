<?php

namespace App\Http\Controllers\Tienda;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Course\Category;
use App\Models\CoursesStudents;
use App\Models\Discount\Discount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Ecommerce\Course\CourseHomeResource;
use App\Http\Resources\Ecommerce\Course\CourseHomeCollection;
use App\Http\Resources\Ecommerce\LandingCourse\LandingCourseResource;

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
            $DISCOUNT_FLASH->end_date = Carbon::parse($DISCOUNT_FLASH->end_date)->addDays(1);
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
                    "count_courses" => $category->count_courses
                ];
            }),
            "courses_home" => CourseHomeCollection::make($courses),
            "group_category_courses" => $group_category_courses,
            "DISCOUNT_BANNER" => $DISCOUNT_BANNER,
            "DISCOUNT_BANNER_COURSES" => $DISCOUNT_BANNER_COURSES,
            "DISCOUNT_FLASH" => $DISCOUNT_FLASH ? [
                "id" => $DISCOUNT_FLASH->id,
                "discount" => $DISCOUNT_FLASH->discount,
                "code" => $DISCOUNT_FLASH->code,
                "type_campaign" => $DISCOUNT_FLASH->type_campaign,
                "type_discount" => $DISCOUNT_FLASH->type_discount,
                "end_date" => Carbon::parse($DISCOUNT_FLASH->end_date)->format("Y-m-d"),
                "start_date_d" => Carbon::parse($DISCOUNT_FLASH->start_date)->format("Y/m/d"),
                "end_date_d" => Carbon::parse($DISCOUNT_FLASH->end_date)->subDays(1)->format("Y/m/d")
            ] : NULL,
            "DISCOUNT_FLASH_COURSES" => $DISCOUNT_FLASH_COURSES
        ]);
    }

    public function course_details(Request $request,$slug){
        $campaign_discount = $request->get("campaign_discount");
        $discount = null;
        if($campaign_discount){
            $discount = Discount::findOrFail($campaign_discount);
        }
        $course = Course::where("slug",$slug)->first();
        $has_course = false;
        if(!$course){
            return abort(404);
        }
        if(Auth::guard('api')->check()){
            $course_student = CoursesStudents::where("user_id",auth('api')->user()->id)->where("course_id",$course->id)->first();
            if($course_student){
                $has_course = true;
            }
        }
        $courses_related_instructor = Course::where("id","<>",$course->id)->where("user_id",$course->user_id)->inRandomOrder()->take(2)->get();
        $courses_related_categories = Course::where("id","<>",$course->id)->where("category_id",$course->category_id)->inRandomOrder()->take(3)->get();

        return response()->json([
            "course" => LandingCourseResource::make($course),
            "courses_related_instructor" => $courses_related_instructor->map(function($course){
                return CourseHomeResource::make($course);
            }),
            "courses_related_categories" => $courses_related_categories->map(function($course){
                return CourseHomeResource::make($course);
            }),
            "DISCOUNT" => $discount,
            "has_course" => $has_course
        ]);
    }

    public function course_leason(Request $request,$slug){
        
        $course = Course::where("slug",$slug)->first();

        if(!$course){
            return response()->json(["message" => 403, "message_text" => "EL CURSO NO EXISTE"]);
        }
        
        $course_student = CoursesStudents::where("course_id",$course->id)->where("user_id",auth('api')->user()->id)->first();
        if(!$course_student){
            return response()->json(["message" => 403, "message_text" => "NO ESTÁS INSCRITO EN ESTE CURSO"]);
        }

        return response()->json([
            "course" => LandingCourseResource::make($course)
        ]);
    }

    public function listCourses(Request $request){
        $search = $request->search;
        $selected_categories = $request->selected_categories ?? [];
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $selected_idiomas = $request->selected_idiomas ?? [];
        $selected_levels = $request->selected_levels ?? [];
        $selected_rating = $request->selected_rating;

        $courses_a = [];
        if($selected_rating){
            $courses_query = Course::where("state",2)->join("reviews","reviews.course_id","=","courses.id")
            ->select("courses.id as courseId",DB::raw("AVG(reviews.rating) as review_rating"))
            ->groupBy("courseId")->having("review_rating",">=",$selected_rating)
            ->having("review_rating","<",$selected_rating + 1)->get();
            
            $courses_a = $courses_query->pluck("courseId")->toArray();
        }
       
        $courses = Course::filterAdvanceEcommerce($search,$selected_categories,$min_price,$max_price,
        $selected_idiomas,$selected_levels,$courses_a,$selected_rating)->where("state",2)->orderBy("id","desc")->get();
        return response()->json(["courses" => CourseHomeCollection::make($courses)]);
    }

    public function config_all(){
        $categories = Category::where("category_id", NULL)->withCount("courses")->orderBy("id", "desc")->get();
        return response()->json([
            "categories" => $categories,
            "levels" => ["Básico","Intermedio","Avanzado"],
            "idiomas" => ["Español","Inglés","Francés","Portugués"],
        ]);
    }
}
