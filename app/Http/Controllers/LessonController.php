<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use DB;
use Illuminate\Http\Request;
use LessonFacade;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request()->all();
        try
        {
            if( ! userHasAccess(auth()->user()) )
                $query['is_public'] = true;

            $lessons = LessonFacade::getList($query);
            $count = LessonFacade::getCount($query);

            return successResponse(LessonResource::collection($lessons), $count);
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
            'course_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
            'file' => 'required|mimes:pdf,docx',
        ]);

        try
        {
            $lesson = LessonFacade::store($data);
            $count = LessonFacade::getCount();

            LessonFacade::addMedia($lesson, $data['file']);

            DB::commit();
            return successResponse(new LessonResource($lesson), $count, "تم الإنشاء بنجاح", 201);
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
    public function show(Lesson $lesson)
    {
        try
        {
            $count = LessonFacade::getCount();

            return successResponse(new LessonResource($lesson), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson)
    {
        DB::beginTransaction();
        $data = $request->validate([
            'course_id' => 'nullable|numeric',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
            'file' => 'nullable|mimes:pdf,docx',
        ]);

        try
        {
            $lesson = LessonFacade::edit($data, $lesson);
            $count = LessonFacade::getCount();

            if(key_exists('file', $data))
            {
                LessonFacade::editMedia($lesson, $data['file']);
            }

            DB::commit();
            return successResponse(new LessonResource($lesson->refresh()), $count, "تم التعديل بنجاح");
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
    public function destroy(Lesson $lesson)
    {
        DB::beginTransaction();
        try
        {
            LessonFacade::deleteMedia($lesson, 'images');

            LessonFacade::delete($lesson);

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
