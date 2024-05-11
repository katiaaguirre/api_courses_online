<?php

namespace App\Http\Controllers\Tienda;

use App\Models\User;
use App\Models\Sale\Sale;
use App\Models\Sale\Review;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\CoursesStudents;
use App\Models\Sale\SalesDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Ecommerce\Sale\SaleCollection;
use App\Http\Resources\Ecommerce\Course\CourseHomeResource;

class ProfileClientController extends Controller
{
    public function profile(Request $request){
        $user = auth('api')->user();

        $enrolled_course_count = CoursesStudents::where("user_id",$user->id)->count();
        $active_course_count = CoursesStudents::where("user_id",$user->id)->where("checked_clases","<>",NULL)->count();
        $completed_course_count = CoursesStudents::where("user_id",$user->id)->where("state",2)->count();

        $enrolled_courses = CoursesStudents::where("user_id",$user->id)->get();
        $active_courses = CoursesStudents::where("user_id",$user->id)->where("checked_clases","<>",NULL)->get();
        $completed_courses = CoursesStudents::where("user_id",$user->id)->where("state",2)->get();

        $sale_details = SalesDetails::whereHas("sale",function($q) use($user){
            $q->where("user_id",$user->id);
        })->orderBy("id","desc")->get();

        $sales = Sale::where("user_id",$user->id)->orderBy("id","desc")->get();

        return response()->json([
            "user" => [
                "name" => $user->name,
                "surname" => $user->surname ?? '',
                "email" => $user->email,
                "phone" => $user->phone,
                "profesion" => $user->profesion,
                "description" => $user->description,
                "avatar" => $user->avatar ? env("APP_URL")."storage/".$user->avatar : null
            ],
            "enrolled_course_count" => $enrolled_course_count,
            "active_course_count" => $active_course_count,
            "completed_course_count" => $completed_course_count,
            "enrolled_courses" => $enrolled_courses->map(function($course_student){
                $checked_clases = $course_student->checked_clases ? explode(",",$course_student->checked_clases) : [];
                return [
                    "id" => $course_student->id,
                    "checked_clases" => $checked_clases,
                    "percentage" => $course_student->course->count_class > 0 ? round((sizeof($checked_clases)/$course_student->course->count_class)*100,2) : 0,
                    "course" => CourseHomeResource::make($course_student->course),
                ];
            }),
            "active_courses" => $active_courses->map(function($course_student){
                $checked_clases = $course_student->checked_clases ? explode(",",$course_student->checked_clases) : [];
                return [
                    "id" => $course_student->id,
                    "checked_clases" => $checked_clases,
                    "percentage" => round((sizeof($checked_clases)/$course_student->course->count_class)*100,2),
                    "course" => CourseHomeResource::make($course_student->course),
                ];
            }),
            "completed_courses" => $completed_courses->map(function($course_student){
                $checked_clases = $course_student->checked_clases ? explode(",",$course_student->checked_clases) : [];
                return [
                    "id" => $course_student->id,
                    "checked_clases" => $checked_clases,
                    "percentage" => round((sizeof($checked_clases)/$course_student->course->count_class)*100,2),
                    "course" => CourseHomeResource::make($course_student->course),
                ];
            }),
            "sale_details" => $sale_details->map(function($sale_detail){
                return [
                    "id" => $sale_detail->id,
                    "review" => $sale_detail->review,
                    "course" => [
                        "id" => $sale_detail->course->id,
                        "title" => $sale_detail->course->title,
                        "image" => env("APP_URL")."storage/".$sale_detail->course->image
                    ],
                    "created_at" => $sale_detail->created_at->format("Y-m-d h:i:s"),
                ];
            }),
            "sales" => SaleCollection::make($sales)
        ]);
    }

    public function HeaderExtras(Request $request){
        $user = auth('api')->user();

        $continuar_viendo_courses = CoursesStudents::with('course')
        ->where("user_id", $user->id)
        ->whereNotNull("checked_clases")
        ->orderBy('updated_at', 'desc')
        ->take(3)
        ->get();


        $continuar_viendo_courses = $continuar_viendo_courses->map(function($coursesStudent){
            $checked_clases = $coursesStudent->checked_clases ? explode(",",$coursesStudent->checked_clases) : [];
            $percentage = $coursesStudent->course->count_class > 0 ? round((sizeof($checked_clases)/$coursesStudent->course->count_class)*100,2) : 0;
            return [
                "title" => $coursesStudent->course->title,
                "slug" => $coursesStudent->course->slug,
                "image" => $coursesStudent->course->image ? env("APP_URL")."storage/".$coursesStudent->course->image : null,
                "percentage" => $percentage,
            ];
        });

        return response()->json([
            "user" => [
                "avatar" => $user->avatar ? env("APP_URL")."storage/".$user->avatar : null
            ],
            "CONTINUAR_VIENDO" => $continuar_viendo_courses,
            
        ]);

    }

    public function update_client(Request $request){
        $user = User::findOrFail(auth('api')->user()->id);
        if ($request->name || $request->surname) {
            $baseSlug = Str::slug($request->name . '-' . $request->surname);
            $slug = $baseSlug;
            $suffix = 1;
    
            while (User::where('slug', $slug)->where('id', '<>', $user->id)->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }
    
            $user->slug = $slug;
        }
        if($request->new_password){
            $request->request->add(["password" => Hash::make($request->new_password)]);
        }
        if($request->hasFile("UImage")){
            if($user->avatar){
                Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users",$request->file("UImage"));
            $request->request->add(["avatar" => $path]);
        }
        $user->update($request->all());
        return response()->json(["message" => 200]);
    }

    public function instructor(Request $request,$slug){
        $instructor = User::where('slug',$slug)->where('is_instructor',1)->first();
        if(!$instructor){
            return response()->json(["message" => 404, "message_text" => "El instructor no existe en la base de datos"]);
        }
        
        return response([
            "instructor" => [
                "full_name" => $instructor->name.' '.$instructor->surname,
                "avatar" =>  env("APP_URL")."storage/".$instructor->avatar,
                "description" => $instructor->description,
                "count_courses" => $instructor->count_courses,
                "count_students" => $instructor->count_students,
                "count_reviews" => $instructor->count_reviews,
                "avg_review" => $instructor->avg_review ? round($instructor->avg_review,2) : 0,
                "courses" =>  $instructor->courses->map(function($course){
                    return [
                        "id" => $course->id,
                        "title" => $course->title,
                        "slug" => $course->slug,
                        "subtitle" => $course->subtitle,
                        "image" => env("APP_URL")."storage/".$course->image,
                        "precio_mxn" => $course->precio_mxn,
                        "avg_review" => $course->avg_review,
                        "count_students" => $course->count_students,
                        "count_reviews" => $course->count_reviews,
                        "count_class" => $course->count_class,
                        "course_time" => $course->course_time,
                    ];
                })
            ],

        ]);
    }

    public function MyLearning(Request $request){
        $user = auth('api')->user();

        $myLearning = CoursesStudents::with('course')
        ->where("user_id", $user->id)
        ->whereNotNull("checked_clases")
        ->orderBy('updated_at', 'desc')
        ->get();


        $myLearning = $myLearning->map(function($coursesStudent) {
            $user = auth('api')->user();
            $checked_clases = $coursesStudent->checked_clases ? explode(",",$coursesStudent->checked_clases) : [];
            $percentage = $coursesStudent->course->count_class > 0 ? round((sizeof($checked_clases)/$coursesStudent->course->count_class)*100,2) : 0;
            $hasReviewed = Review::where('user_id',$user->id)->where('course_id', $coursesStudent->course->id)->first();
            return [
                "title" => $coursesStudent->course->title,
                "slug" => $coursesStudent->course->slug,
                "image" => $coursesStudent->course->image ? env("APP_URL")."storage/".$coursesStudent->course->image : null,
                "percentage" => $percentage,
                "review" => $hasReviewed ? [
                    "id" => $hasReviewed->id,
                    "rating" => $hasReviewed->rating
                ] : null,
                "instructor" => $coursesStudent->course->instructor ? [
                    "full_name" => $coursesStudent->course->instructor->name. ' '. $coursesStudent->course->instructor->surname,
                    "slug" => $coursesStudent->course->instructor->slug
                ] : null 
            ];
        });
        

        return response()->json([
            "MyLearning" => $myLearning
        ]);

    }
}
