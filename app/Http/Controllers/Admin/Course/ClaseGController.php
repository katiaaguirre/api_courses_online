<?php

namespace App\Http\Controllers\Admin\Course;

use Illuminate\Http\Request;
use App\Models\Course\CourseClase;
use App\Http\Controllers\Controller;
use App\Models\Course\CourseClaseFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Course\Clases\CourseClaseResource;
use App\Http\Resources\Course\Clases\CourseClaseCollection;

class ClaseGController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clases = CourseClase::where("course_section_id", $request->course_section_id)->orderBy("id", "asc")->get();
        return response()->json([
            "clases" => CourseClaseCollection::make($clases)
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
        $clase = CourseClase::create($request->all());

        if($request->hasFile('files')){
            foreach($request->file("files") as $key => $file){
                $extension = $file->getClientOriginalExtension();
                $size = $file->getSize();
                $file_name = $file->getClientOriginalName();
                $data = null;
                if(in_array(strtolower($extension), ["jpeg", "bmp", "jpg", "png"])){
                    $data = getimagesize($file);
                }
                $path = Storage::putFile("clases_files", $file);
                $clase_file = CourseClaseFile::create([
                    "course_clase_id" => $clase->id,
                    "name_file" => $file_name,
                    "size" => $size,
                    "resolution" => $data ? $data[0]." X ".$data[1]: NULL,
                    "file" => $path,
                    "type" => $extension
                ]);
            }
        }
        
        return response()->json(["clase" => CourseClaseResource::make($clase)]);
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
        $clase = CourseClase::findOrFail($id);
        $clase->update($request->all());
        return response()->json(["clase" => CourseClaseResource::make($clase)]);
    }


        public function AddFiles(Request $request)
        {
            $clase = CourseClase::findOrFail($request->course_clase_id);
            if($request->hasFile('files')){
            foreach ($request->file('files') as $key => $file) {
                $extension = $file->getClientOriginalExtension();
                $size = $file->getSize();
                $file_name = $file->getClientOriginalName();
                $data = null;

                if (in_array(strtolower($extension), ["jpeg", "bmp", "jpg", "png"])) {
                    $data = getimagesize($file);
                }

                $path = Storage::putFile("clases_files", $file);

                $clase_file = CourseClaseFile::create([
                    "course_clase_id" => $clase->id,
                    "name_file" => $file_name,
                    "size" => $size,
                    "resolution" => $data ? $data[0]." X ".$data[1] : NULL,
                    "file" => $path,
                    "type" => $extension
                ]);
                }
            }

            return response()->json(["clase" => CourseClaseResource::make($clase->load('files'))]);
        }


    public function RemoveFiles($id){
        $course_clase_file = CourseClaseFile::findOrFail($id);
        Storage::delete($course_clase_file->file);
        $course_clase_file->delete();
    
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

        $clase = CourseClase::findOrFail($id);

        $claseFiles = CourseClaseFile::where('course_clase_id', $clase->id)->get();
        foreach ($claseFiles as $claseFile) {
            Storage::delete($claseFile->file);
            $claseFile->delete();
        }

        $clase->delete();

        return response()->json(["message" => 200]);

    }
}
