<?php

namespace App\Http\Controllers\Tienda;

use App\Models\Sale\Cart;
use Illuminate\Http\Request;
use App\Models\Coupon\Coupon;
use App\Models\CoursesStudents;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ecommerce\Cart\CartResource;
use App\Http\Resources\Ecommerce\Cart\CartCollection;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();

        $carts = Cart::where("user_id",$user->id)->get();

        return response()->json(["carts" => CartCollection::make($carts)]);
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
        $user = auth('api')->user();
        $has_course = CoursesStudents::where("user_id",$user->id)->where("course_id",$request->course_id)->first();
        $in_cart = Cart::where("user_id",$user->id)->where("course_id",$request->course_id)->first();
        if($in_cart){
            return response()->json(["message" => 403, "message_text" => "El curso ya existe en la lista"]);
        }
        if($has_course){
            return response()->json(["message" => 403, "message_text" => "Ya has adquirido este curso"]);
        }
        
        $request->request->add(["user_id" => $user->id]);
        $cart = Cart::create($request->all());
        return response()->json(["cart" => CartResource::make($cart)]);
    }

    public function apply_coupon(Request $request){
        $cupon = Coupon::where("code",$request->code)->where("state",1)->first();

        if(!$cupon){
            return response()->json(["message" => 403, "message_text" => "El cupón ingresado no existe"]);
        }
        $carts = Cart::where("user_id",auth('api')->user()->id)->get();
        foreach($carts as $key => $cart){
            if($cupon->type_coupon == 1){
                $EXISTS_COURSE_COUPON = false;
                foreach($cupon->courses as $key => $course){
                    if($course->course_id == $cart->course_id){
                        $EXISTS_COURSE_COUPON = true;
                        break;
                    }
                }
                if($EXISTS_COURSE_COUPON){
                    $total = 0;
                    if($cupon->type_discount == 1){
                        $total = $cart->precio_unitario - $cart->precio_unitario*($cupon->discount*0.01);
                    }
                    if($cupon->type_discount == 2){
                        $total = $cart->precio_unitario - $cupon->discount;
                    }
                    $cart->update([
                        "type_discount" => $cupon->type_discount,
                        "discount" => $cupon->discount,
                        "type_campaign" => NULL,
                        "coupon_code" => $cupon->code,
                        "discount_code" => NULL,
                        "total" => $total
                    ]);

                    
                }
            }
            if($cupon->type_coupon == 2){
                $EXISTS_CATEGORY_COUPON = false;
                foreach($cupon->categories as $key => $category){
                    if($category->category_id == $cart->course->category_id){
                        $EXISTS_CATEGORY_COUPON = true;
                        break;
                    }
                }
                if($EXISTS_CATEGORY_COUPON){
                    $total = 0;
                    if($cupon->type_discount == 1){
                        $total = $cart->precio_unitario - $cart->precio_unitario*($cupon->discount*0.01);
                    }
                    if($cupon->type_discount == 2){
                        $total = $cart->precio_unitario - $cupon->discount;
                    }
                    $cart->update([
                        "type_discount" => $cupon->type_discount,
                        "discount" => $cupon->discount,
                        "type_campaign" => NULL,
                        "coupon_code" => $cupon->code,
                        "discount_code" => NULL,
                        "total" => $total
                    ]);
                }
            }
        }
        $carts = Cart::where("user_id",auth('api')->user()->id)->get();
        return response()->json(["carts" => CartCollection::make($carts)]);
    }

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return response()->json(["message" => 200]);
    }
}
