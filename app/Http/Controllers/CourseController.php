<?php

namespace App\Http\Controllers;

use App\Facades\Course\CourseFacade;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use DB;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request()->all();
        try
        {
            $courses = CourseFacade::getList($query);
            $count = CourseFacade::getCount($query);

            return successResponse(CourseResource::collection($courses), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester_id' => 'required|numeric',
            'academic_year_id' => 'required|numeric',
            'image' => 'required|mimes:png,jpg,bmp',
        ]);

        try
        {
            $course = CourseFacade::store($data);
            $count = CourseFacade::getCount();

            CourseFacade::addMedia($course, $data['image']);

            DB::commit();
            return successResponse(new CourseResource($course), $count, "تم الإنشاء بنجاح", 201);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        try
        {
            $course = CourseFacade::getSingle($course);
            $count = CourseFacade::getCount();

            return successResponse(new CourseResource($course), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        DB::beginTransaction();
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'semester_id' => 'nullable|numeric',
            'academic_year_id' => 'nullable|numeric',
            'image' => 'nullable|mimes:png,jpg,bmp',
        ]);

        try
        {
            $course = CourseFacade::edit($data, $course);
            $count = CourseFacade::getCount();

            if(key_exists('image', $data))
            {
                CourseFacade::editMedia($course, $data['image']);
            }

            DB::commit();
            return successResponse(new CourseResource($course->refresh()), $count, "تم التعديل بنجاح");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        DB::beginTransaction();
        try
        {
            CourseFacade::deleteMedia($course, 'images');

            CourseFacade::delete($course);

            DB::commit();
            return successResponse(message: "تم الحذف بنجاح");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }
}
