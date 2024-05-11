<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Sale\Sale;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\CoursesStudents;
use App\Models\Sale\SalesDetails;
use App\Http\Controllers\Controller;
use App\Models\Repertorio\Repertorio;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\User\UserGResource;
use App\Http\Resources\User\UserGCollection;
use App\Models\Repertorio\RepertorioStudents;

class UserController extends Controller
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
        
        $users = User::filterAdvance($search, $state)->where("type_user", 2)->orderby("id", "asc")->get();

        return response()->json([
            "users" => UserGCollection::make($users)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    public function roles(){
        $roles = Role::orderBy("id", "asc")->get();
    
        return response()->json($roles);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $slug = Str::slug($request->name . '-' . $request->surname);
        $baseSlug = $slug;
        $suffix = 1;

        while (User::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $request->merge(['slug' => $slug]);

        if($request->hasFile("imagen")){
            $path = Storage::putFile("users", $request->file("imagen"));
            $request->request->add(["avatar" => $path]);
        }
        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }
        $user = User::create($request->all());

        return response()->json(["user" => UserGResource::make($user)]);
    }

    

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $slug = Str::slug($request->name . '-' . $request->surname);
        $baseSlug = $slug;
        $suffix = 1;

        while (User::where('slug', $slug)->where('id', '<>', $id)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $request->merge(['slug' => $slug]);
        
        if($request->hasFile("imagen")){
            if($user->avatar){
               Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users", $request->file("imagen"));
            $request->request->add(["avatar" => $path]);
        }
        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }
        $user->update($request->all());
        return response()->json(["user" => UserGResource::make($user)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if($user->avatar){
            Storage::delete($user->avatar);
         }
        $user->delete();
        return response()->json(["message" => 200]);
    }

    public function AddToRep(Request $request){
        $usuario = $request->usuario;
        $UserOption = $request->OpcionUsuario;
        $repertorio = $request->repertorio;
        $RepertorioOption = $request->OpcionRepertorio;
    
        if($UserOption == 1){
            $existeUsuario = User::where("email",$usuario)->first();
        } elseif($UserOption == 2){
            $existeUsuario = User::find($usuario);
        }
    
        if(!$existeUsuario){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL USUARIO"]);
        }
        $usuario_id = $existeUsuario->id;
    
        if($RepertorioOption == 1){
            $existeRepertorio = Repertorio::where("slug",$repertorio)->first();
        } elseif($RepertorioOption == 2){
            $existeRepertorio = Repertorio::find($repertorio);
        }
    
        if(!$existeRepertorio){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL REPERTORIO"]);
        }
        $repertorio_id = $existeRepertorio->id;
        
        $ya_inscrito = RepertorioStudents::where("user_id",$usuario_id)->where("repertorio_id",$repertorio_id)->first();
        if($ya_inscrito){
            return response()->json(["message" => 403, "message_text" => "EL USUARIO YA ESTÁ INSCRITO EN EL REPERTORIO"]);
        }

        RepertorioStudents::create([
            "user_id" => $usuario_id,
            "repertorio_id" => $repertorio_id,
            "instrumento" => json_encode($request->instrumento)
        ]);
    
        return response()->json(["message" => 200]);
    }
    

    public function RemoveFromRep(Request $request){
        $usuario = $request->usuario;
        $UserOption = $request->OpcionUsuario;
        $repertorio = $request->repertorio;
        $RepertorioOption = $request->OpcionRepertorio;
        $usuario_id = null;
        $repertorio_id = null;
    
        if($UserOption == 1){
            $existeUsuario = User::where("email",$usuario)->first();
        } elseif($UserOption == 2){
            $existeUsuario = User::find($usuario);
        }
    
        if(!$existeUsuario){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL USUARIO"]);
        }
        $usuario_id = $existeUsuario->id;
    
        if($RepertorioOption == 1){
            $existeRepertorio = Repertorio::where("slug",$repertorio)->first();
        } elseif($RepertorioOption == 2){
            $existeRepertorio = Repertorio::find($repertorio);
        }
    
        if(!$existeRepertorio){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL REPERTORIO"]);
        }
        $repertorio_id = $existeRepertorio->id;
        
        $ya_inscrito = RepertorioStudents::where("user_id",$usuario_id)->where("repertorio_id",$repertorio_id)->first();
        if($ya_inscrito){
            $ya_inscrito->delete();
            return response()->json(["message" => 200]);
        }else{
            return response()->json(["message" => 403, "message_text" => "EL USUARIO NO ESTÁ INSCRITO EN EL REPERTORIO"]);
        }
    }

    public function AddToCourse(Request $request){
        $usuario = $request->usuario;
        $UserOption = $request->OpcionUsuario;
        $curso = $request->curso;
        $CursoOption = $request->OpcionCurso;
        $usuario_id = null;
        $curso_id = null;
    
        if($UserOption == 1){
            $existeUsuario = User::where("email",$usuario)->first();
        } elseif($UserOption == 2){
            $existeUsuario = User::find($usuario);
        }
    
        if(!$existeUsuario){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL USUARIO"]);
        }
        $usuario_id = $existeUsuario->id;
    
        if($CursoOption == 1){
            $existeCurso = Course::where("slug",$curso)->first();
        } elseif($CursoOption == 2){
            $existeCurso = Course::find($curso);
        }
    
        if(!$existeCurso){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL CURSO"]);
        }
        $curso_id = $existeCurso->id;
        
        $ya_inscrito = CoursesStudents::where("user_id",$usuario_id)->where("course_id",$curso_id)->first();
        if($ya_inscrito){
            return response()->json(["message" => 403, "message_text" => "EL USUARIO YA ESTÁ INSCRITO EN EL CURSO"]);
        }

        $sale = new Sale();
        $sale->user_id = $usuario_id;
        $sale->payment_method = "INSCRIPCIÓN MANUAL";
        $sale->total = 0;
        $sale->save();

        $saleDetail = new SalesDetails();
        $saleDetail->sale_id = $sale->id;
        $saleDetail->course_id = $curso_id;
        $saleDetail->user_id = $usuario_id;
        $saleDetail->notas = "INSCRIPCIÓN MANUAL";
        $saleDetail->total = 0;
        $saleDetail->save();

        CoursesStudents::create([
            "user_id" => $usuario_id,
            "course_id" => $curso_id
        ]);
    
        return response()->json(["message" => 200]);
    }

    public function RemoveFromCourse(Request $request){
        $usuario = $request->usuario;
        $UserOption = $request->OpcionUsuario;
        $curso = $request->curso;
        $CursoOption = $request->OpcionCurso;
        $usuario_id = null;
        $curso_id = null;
    
        if($UserOption == 1){
            $existeUsuario = User::where("email",$usuario)->first();
        } elseif($UserOption == 2){
            $existeUsuario = User::find($usuario);
        }
    
        if(!$existeUsuario){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL USUARIO"]);
        }
        $usuario_id = $existeUsuario->id;
    
        if($CursoOption == 1){
            $existeCurso = Course::where("slug",$curso)->first();
        } elseif($CursoOption == 2){
            $existeCurso = Course::find($curso);
        }
    
        if(!$existeCurso){
            return response()->json(["message" => 403, "message_text" => "NO SE ENCONTRÓ EL CURSO"]);
        }
        $curso_id = $existeCurso->id;
        
        $ya_inscrito = CoursesStudents::where("user_id", $usuario_id)->where("course_id", $curso_id)->first();
        if(!$ya_inscrito){
            return response()->json(["message" => 403, "message_text" => "EL USUARIO NO ESTÁ INSCRITO EN EL CURSO"]);
        }

        $salesDetails = SalesDetails::where("user_id", $usuario_id)->where("course_id", $curso_id)->first();
        
        if($salesDetails){
            $sale = Sale::find($salesDetails->sale_id);
            if($sale){
                $sale->delete();
                $sale = null;
            }
            $salesDetails->delete();
            $salesDetails = null;
        }
        $ya_inscrito->delete();
    
        return response()->json(["message" => 200]);

    }

    public function AddToMAmigo(Request $request){
        
    }

    public function RemoveFromMAmigo(Request $request){
        
    }

    public function CreateRole(Request $request){
        $id = $request->id;
        $name = $request->name;
        $subtitle = $request->subtitle;

        $sameName = Role::where("name",$name)->first();
        $sameId = Role::where("id",$id)->first();
        if($sameName){
            return response()->json(["message" => 403, "message_text" => "YA EXISTE UN ROL CON ESE NOMBRE"]);
        }
        if($sameId){
            return response()->json(["message" => 403, "message_text" => "YA EXISTE UN ROL CON ESE ID"]);
        }

        Role::create([
            "id" => $id,
            "name" => $name,
            "subtitle" => $subtitle
        ]);

        return response()->json(["message" => 200]);
    }

    public function DeleteRole(Request $request){
        $id = $request->id;
        $name = $request->name;
    
        if (!$id && !$name) {
            return response()->json(["message" => 400, "message_text" => "DEBE INGRESAR AL MENOS UN VALOR (ID O NOMBRE)"]);
        }
    
        $rol = null;
        if ($id && $name) {
            $rol = Role::where("id", $id)->where("name", $name)->first();
            if (!$rol) {
                return response()->json(["message" => 403, "message_text" => "NO EXISTE UN ROL QUE TENGA EL ID Y NOMBRE INDICADOS"]);
            }
        } elseif ($id) {
            $rol = Role::where("id", $id)->first();
        } elseif ($name) {
            $rol = Role::where("name", $name)->first();
        }
    
        if (!$rol) {
            return response()->json(["message" => 403, "message_text" => "NO EXISTE UN ROL CON ESE NOMBRE O ID"]);
        }
    
        $rol->delete();
    
        return response()->json(["message" => 200]);
    }
    
}
