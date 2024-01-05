<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use DB;
use Illuminate\Http\Request;
use QuestionFacade;

class QuestionController extends Controller
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

            $questions = QuestionFacade::getList($query);
            $count = QuestionFacade::getCount($query);

            return successResponse(QuestionResource::collection($questions), $count);
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
            'semester_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
            'file' => 'required|mimes:pdf,docx',
        ]);

        try
        {
            $question = QuestionFacade::store($data);
            $count = QuestionFacade::getCount();

            QuestionFacade::addMedia($question, $data['file']);

            DB::commit();
            return successResponse(new QuestionResource($question), $count, "تم الإنشاء بنجاح", 201);
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
    public function show(Question $question)
    {
        try
        {
            $count = QuestionFacade::getCount();

            return successResponse(new QuestionResource($question), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        DB::beginTransaction();
        $data = $request->validate([
            'course_id' => 'nullable|numeric',
            'semester_id' => 'nullable|numeric',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
            'file' => 'nullable|mimes:pdf,docx',
        ]);

        try
        {
            $question = QuestionFacade::edit($data, $question);
            $count = QuestionFacade::getCount();

            if(key_exists('file', $data))
            {
                QuestionFacade::editMedia($question, $data['file']);
            }

            DB::commit();
            return successResponse(new QuestionResource($question->refresh()), $count, "تم التعديل بنجاح");
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
    public function destroy(Question $question)
    {
        DB::beginTransaction();
        try
        {
            QuestionFacade::deleteMedia($question, 'images');

            QuestionFacade::delete($question);

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
