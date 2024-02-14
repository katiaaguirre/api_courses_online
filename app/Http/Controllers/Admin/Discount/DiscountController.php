<?php

namespace App\Http\Controllers\Admin\Discount;

use Illuminate\Http\Request;
use App\Models\Discount\Discount;
use App\Http\Controllers\Controller;
use App\Models\Discount\DiscountCourse;
use App\Models\Discount\DiscountCategory;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Discount\DiscountCollection;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $state = $request->state;
        $discounts = Discount::orderBy("id","desc")->get();

        return response()->json(["message" => 200, "discounts" => DiscountCollection::make($discounts)]);
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
        if($request->discount_type == 1){
            foreach($request->course_selected as $key => $course){
                $DISCOUNT_START_DATE = Discount::where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("courses",function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();
                $DISCOUNT_END_DATE = Discount::where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("courses",function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("end_date",[$request->end_date,$request->end_date])->first();
                if($DISCOUNT_START_DATE || $DISCOUNT_END_DATE){
                    return response()->json(["message" => 403, "message_text" => "EL CURSO '". $course["title"]. "' YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO - ". ($DISCOUNT_START_DATE ? $DISCOUNT_START_DATE->id : '-').($DISCOUNT_END_DATE ? $DISCOUNT_END_DATE->id : '')]);
                }
            }
        }

        if($request->discount_type == 2){
            foreach($request->category_selected as $key => $category){
                $DISCOUNT_START_DATE = Discount::where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("categories",function($q) use($category){
                    return $q->where("category_id",$category["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();
                $DISCOUNT_END_DATE = Discount::where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("categories",function($q) use($category){
                    return $q->where("category_id",$category["id"]);
                })->whereBetween("end_date",[$request->end_date,$request->end_date])->first();
                if($DISCOUNT_START_DATE || $DISCOUNT_END_DATE){
                    return response()->json(["message" => 403, "message_text" => "LA CATEGORÍA '". $category["name"]. "' YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO - ". ($DISCOUNT_START_DATE ? $DISCOUNT_START_DATE->id : '-').($DISCOUNT_END_DATE ? $DISCOUNT_END_DATE->id : '')]);
                }
            }
        }

        $request->request->add(["code" => uniqid()]);
        $discount = Discount::create($request->all());

        if ($request->discount_type == 1 && !empty($request->course_selected)) {
            foreach ($request->course_selected as $key => $course) {
                DiscountCourse::create([
                    "discount_id" => $discount->id,
                    "course_id" => $course["id"]
                ]);
            }
        }
        if ($request->discount_type == 2 && !empty($request->category_selected)) { 
            foreach ($request->category_selected as $key => $category) {
                DiscountCategory::create([
                    "discount_id" => $discount->id,
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
        $discount = Discount::findOrFail($id);

        return response()->json([
            "discount" => DiscountResource::make($discount)
        ]);
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
        
        if($request->discount_type == 1){
            foreach($request->course_selected as $key => $course){
                $DISCOUNT_START_DATE = Discount::where("id", "<>", $id)->where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("courses",function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();
                $DISCOUNT_END_DATE = Discount::where("id", "<>", $id)->where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("courses",function($q) use($course){
                    return $q->where("course_id",$course["id"]);
                })->whereBetween("end_date",[$request->end_date,$request->end_date])->first();
                if($DISCOUNT_START_DATE || $DISCOUNT_END_DATE){
                    return response()->json(["message" => 403, "message_text" => "EL CURSO '". $course["title"]. "' YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO - ". ($DISCOUNT_START_DATE ? $DISCOUNT_START_DATE->id : '-').($DISCOUNT_END_DATE ? $DISCOUNT_END_DATE->id : '')]);
                }
            }
        }

        if($request->discount_type == 2){
            foreach($request->category_selected as $key => $category){
                $DISCOUNT_START_DATE = Discount::where("id", "<>", $id)->where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("categories",function($q) use($category){
                    return $q->where("category_id",$category["id"]);
                })->whereBetween("start_date",[$request->start_date,$request->end_date])->first();
                $DISCOUNT_END_DATE = Discount::where("id", "<>", $id)->where("type_campaign", $request->type_campaign)->where("discount_type", $request->discount_type)->whereHas("categories",function($q) use($category){
                    return $q->where("category_id",$category["id"]);
                })->whereBetween("end_date",[$request->end_date,$request->end_date])->first();
                if($DISCOUNT_START_DATE || $DISCOUNT_END_DATE){
                    return response()->json(["message" => 403, "message_text" => "LA CATEGORÍA '". $category["name"]. "' YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO - ". ($DISCOUNT_START_DATE ? $DISCOUNT_START_DATE->id : '-').($DISCOUNT_END_DATE ? $DISCOUNT_END_DATE->id : '')]);
                }
            }
        }

        $discount = Discount::findOrFail($id);
        $discount->update($request->all());

        foreach($discount->courses as $key => $courseD){
            $courseD->delete();
        }
        
        foreach($discount->categories as $key => $categoryD){
            $categoryD->delete();
        }

        if ($request->discount_type == 1 && !empty($request->course_selected)) {
            foreach ($request->course_selected as $key => $course) {
                DiscountCourse::create([
                    "discount_id" => $discount->id,
                    "course_id" => $course["id"]
                ]);
            }
        }
        if ($request->discount_type == 2 && !empty($request->category_selected)) { 
            foreach ($request->category_selected as $key => $category) {
                DiscountCategory::create([
                    "discount_id" => $discount->id,
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
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json(["message" => 200]);
    }
}
