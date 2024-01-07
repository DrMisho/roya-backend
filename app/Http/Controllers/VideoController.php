<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Models\Course;
use App\Models\Video;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use VideoFacade;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $query = request()->all();
        try
        {
            dd(request()->userAgent());
            $query['course_id'] = $course->id;

            if( ! userHasAccess(auth()->user(), $course) )
                $query['is_public'] = true;

            $videos = VideoFacade::getList($query);
            $count = VideoFacade::getCount($query);

            return successResponse(VideoResource::collection($videos), $count);
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
            'file' => 'required|mimes:mp4,3gp',
        ]);

        try
        {
            $video = VideoFacade::store($data);
            $count = VideoFacade::getCount();

            VideoFacade::addMedia($video, $data['file']);

            DB::commit();
            return successResponse(new VideoResource($video), $count, "تم الإنشاء بنجاح", 201);
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
    public function show(Video $video)
    {
        try
        {
            $course = $video->course;

            if( ! userHasAccess(auth()->user(), $course) )
                $is_public = true;

            try
            {
                $video = VideoFacade::getSingleByQuery([
                    'id' => $video->id,
                    'is_public' => $is_public?? false
                ]);
            }
            catch(ModelNotFoundException $e)
            {
                return failResponse('هذا الفيديو لست مشترك بالمادة الخاصة به', 403);
            }
            $count = VideoFacade::getCount();

            return successResponse(new VideoResource($video), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        DB::beginTransaction();
        $data = $request->validate([
            'course_id' => 'nullable|numeric',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
            'file' => 'nullable|mimes:mp4,3gp',
        ]);

        try
        {
            $video = VideoFacade::edit($data, $video);
            $count = VideoFacade::getCount();

            if(key_exists('file', $data))
            {
                VideoFacade::editMedia($video, $data['file']);
            }

            DB::commit();
            return successResponse(new VideoResource($video->refresh()), $count, "تم التعديل بنجاح");
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
    public function destroy(Video $video)
    {
        DB::beginTransaction();
        try
        {
            VideoFacade::deleteMedia($video, 'images');

            VideoFacade::delete($video);

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
