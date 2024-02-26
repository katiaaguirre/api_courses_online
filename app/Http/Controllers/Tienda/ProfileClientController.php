<?php

namespace App\Http\Controllers\Tienda;

use App\Models\User;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
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
                "avatar" => env("APP_URL")."storage/".$user->avatar
            ],
            "enrolled_course_count" => $enrolled_course_count,
            "active_course_count" => $active_course_count,
            "completed_course_count" => $completed_course_count,
            "enrolled_courses" => $enrolled_courses->map(function($course_student){
                $checked_clases = $course_student->checked_clases ? explode(",",$course_student->checked_clases) : [];
                return [
                    "id" => $course_student->id,
                    "checked_clases" => $checked_clases,
                    "percentage" => round((sizeof($checked_clases)/$course_student->course->count_class)*100,2),
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

    public function update_client(Request $request){
        $user = User::findOrFail(auth('api')->user()->id);
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
}
