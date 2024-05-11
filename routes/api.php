<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Tienda\CartController;
use App\Http\Controllers\Tienda\HomeController;
use App\Http\Controllers\Tienda\ReviewController;
use App\Http\Controllers\Tienda\CheckoutController;
use App\Http\Controllers\Tienda\RepertorioController;
use App\Http\Controllers\Admin\Coupon\CouponController;
use App\Http\Controllers\Admin\Course\ClaseGController;
use App\Http\Controllers\Admin\Course\CourseGController;
use App\Http\Controllers\Tienda\ProfileClientController;
use App\Http\Controllers\Admin\Course\CategoryController;
use App\Http\Controllers\Admin\Course\SectionGController;
use App\Http\Controllers\Admin\Discount\DiscountController;
use App\Http\Controllers\Admin\Repertorio\OpcionGController;
use App\Http\Controllers\Admin\Repertorio\CancionGController;
use App\Http\Controllers\Admin\Repertorio\RepertorioGController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login_tienda', [AuthController::class, 'login_tienda'])->name('login_tienda');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::resource('/users', UserController::class);
    Route::post('/users/{id}', [UserController::class, "update"]);
    Route::get('/roles', [UserController::class, "roles"]);

    Route::resource('/category', CategoryController::class);
    Route::post('/category/{id}', [CategoryController::class, "update"]);

    Route::get('/course/config', [CourseGController::class, "config"]);
    Route::resource('/course', CourseGController::class);
    Route::post('/course/{id}', [CourseGController::class, "update"]);
    Route::resource('/course-section', SectionGController::class);

    Route::resource('/course-clases', ClaseGController::class);
    Route::post('/course-clases-file', [ClaseGController::class, "AddFiles"]);
    Route::delete('/course-clases-file/{id}', [ClaseGController::class, "RemoveFiles"]);

    Route::get('/coupon/config', [CouponController::class, "config"]);
    Route::resource('/coupon', CouponController::class);
    
    Route::resource('/discount', DiscountController::class);

    Route::resource('/repertorio', RepertorioGController::class);
    Route::post('/repertorio/{id}', [RepertorioGController::class, "update"]);
    Route::resource('/repertorio-cancion', CancionGController::class);
    Route::resource('/repertorio-opcion', OpcionGController::class);
    Route::post('/repertorio-opcion-file', [OpcionGController::class, "AddFiles"]);
    Route::delete('/repertorio-opcion-file/{id}', [OpcionGController::class, "RemoveFile"]);

    Route::get("/tienda-repertorio/{slug}", [RepertorioController::class, "repertorio"]);

    Route::post('/add-to-repertorio', [UserController::class, "AddToRep"]);
    Route::post('/remove-from-repertorio', [UserController::class, "RemoveFromRep"]);
    Route::post('/add-to-curso', [UserController::class, "AddToCourse"]);
    Route::post('/remove-from-curso', [UserController::class, "RemoveFromCourse"]);
    Route::post('/add-to-amigo', [UserController::class, "AddToMAmigo"]);
    Route::post('/remove-from-amigo', [UserController::class, "RemoveFromMAmigo"]);
    Route::post('/create-role', [UserController::class, "CreateRole"]);
    Route::post('/delete-role', [UserController::class, "DeleteRole"]);


});

Route::group(["prefix" => "ecommerce"], function($router) {
    Route::get("home", [HomeController::class, "home"]);
    Route::get("config_all", [HomeController::class, "config_all"]);
    Route::post("list_courses", [HomeController::class, "listCourses"]);
    Route::get("course_details/{slug}", [HomeController::class, "course_details"]);
    Route::get('/instructor/{slug}', [ProfileClientController::class, "instructor"]);

    Route::group([
        'middleware' => 'api',
    ], function ($router){
    Route::get("/user", [ProfileClientController::class, "user"]);
    Route::get("course_leason/{slug}", [HomeController::class, "course_leason"]);
    Route::post('/checked_clases/{slug}', [HomeController::class, "CheckedClases"]);
    Route::post('/last_class/{slug}', [HomeController::class, "LastClass"]);
    Route::post('/apply_coupon', [CartController::class, "apply_coupon"]);
    Route::resource('/cart', CartController::class);
    Route::post('/checkout', [CheckoutController::class,"store"]);
    Route::post('/profile', [ProfileClientController::class,"profile"]);
    Route::post('/update_client', [ProfileClientController::class,"update_client"]);
    Route::get('/extras', [ProfileClientController::class, "HeaderExtras"]);
    Route::get('/my_learning', [ProfileClientController::class, "MyLearning"]);

    Route::resource('/review', ReviewController::class);
    });  
});
