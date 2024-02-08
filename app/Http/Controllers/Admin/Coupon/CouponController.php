<?php

namespace App\Http\Controllers\Admin\Coupon;

use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Course\Category;
use App\Models\Coupon\CouponCourse;
use App\Http\Controllers\Controller;
use App\Models\Coupon\CouponCategory;
use App\Http\Resources\Course\Coupon\CouponCollection;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coupons = Coupon::orderBy("id","desc")->get();

        return response()->json(["message" => 200, "coupons" => CouponCollection::make($coupons)]);
    }

    public function config(){
        $categories = Category::where("category_id",null)->orderBy("id","desc")->get();
        $courses = Course::where("state",2)->orderBy("id","desc")->get();
        return response()->json(["categories" => $categories->map(function($category){
            return [
                "id" => $category->id,
                "name" => $category->name,
                "image" => env("APP_URL")."storage/".$category->image
            ];
        }), 
            "courses" => $courses->map(function($course){
                return [
                    "id" => $course->id,
                    "title" => $course->title,
                    "image" => env("APP_URL")."storage/".$course->image
                ];
            })
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $EXISTS = Coupon::where("code", $request->code)->first();
        if($EXISTS){

            return response()->json(["message" => 403, "message_text" => "EL CÓDIGO DEL CUPÓN YA EXISTE"]);
        }

        $coupon = Coupon::create($request->all());

        if($request->type_coupon == 1){ // 1 es course
            foreach ($request->course_selected as $key => $course){
                CouponCourse::create([
                    "coupon_id" => $coupon->id,
                    "course_id" => $course["id"]
                ]);
            }
        }
        if($request->type_coupon == 2){ // 2 es categoría
            foreach ($request->category_selected as $key => $category){
                CouponCategory::create([
                    "coupon_id" => $coupon->id,
                    "category_id" => $category["id"]
                ]);
            }
        }

        return response()->json(["message" => 200]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $EXISTS = Coupon::where("id", "<>", $id)->where("code", $request->code)->first();
        if($EXISTS){

            return response()->json(["message" => 403, "message_text" => "EL CÓDIGO DEL CUPÓN YA EXISTE"]);
        }

        $coupon = Coupon::findOrFail($id);
        
        $coupon->update($request->all());

        foreach($coupon->courses as $key => $courseD){
            $courseD->delete();
        }
        
        foreach($coupon->categories as $key => $categoryD){
            $categoryD->delete();
        }

        if($request->type_coupon == 1){ // 1 es course
            foreach ($request->course_selected as $key => $course){
                CouponCourse::create([
                    "coupon_id" => $coupon->id,
                    "course_id" => $course["id"]
                ]);
            }
        }
        if($request->type_coupon == 2){ // 2 es categoría
            foreach ($request->category_selected as $key => $category){
                CouponCategory::create([
                    "coupon_id" => $coupon->id,
                    "category_id" => $category["id"]
                ]);
            }
        }

        return response()->json(["message" => 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(["message" => 200]);
    }
}
