<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuizResource;
use App\Models\Option;
use App\Models\Quiz;
use DB;
use Illuminate\Http\Request;
use QuizFacade;

class QuizController extends Controller
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

            $quizzes = QuizFacade::getList($query);
            $count = QuizFacade::getCount($query);

            return successResponse(QuizResource::collection($quizzes), $count);
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
            'question' => 'required|string',
            'is_public' => 'nullable|boolean',
            'options' => 'required|array',
        ]);

        try
        {
            $quiz = QuizFacade::store($data);
            $count = QuizFacade::getCount();

            foreach($data['options'] as $option)
            {
                Option::create([
                    'quiz_id' => $quiz->id,
                    'name' => $option['name'],
                    'is_correct' => $option['is_correct'],
                ]);
            }

            DB::commit();
            return successResponse(new QuizResource($quiz), $count, "تم الإنشاء بنجاح", 201);
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
    public function show(Quiz $quiz)
    {
        try
        {
            $count = QuizFacade::getCount();

            return successResponse(new QuizResource($quiz), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        DB::beginTransaction();
        $data = $request->validate([
            'course_id' => 'nullable|numeric',
            'question' => 'nullable|string',
            'is_public' => 'nullable|boolean',
            'options' => 'nullable|array',
        ]);

        try
        {
            $quiz = QuizFacade::edit($data, $quiz);
            $count = QuizFacade::getCount();

            if(key_exists('options', $data))
            {
                $quiz->options()->delete();
                foreach($data['options'] as $option)
                {
                    Option::create([
                        'quiz_id' => $quiz->id,
                        'name' => $option['name'],
                        'is_correct' => $option['is_correct'],
                    ]);
                }
            }

            DB::commit();
            return successResponse(new QuizResource($quiz->refresh()), $count, "تم التعديل بنجاح");
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
    public function destroy(Quiz $quiz)
    {
        DB::beginTransaction();
        try
        {
            $quiz->options()->delete();
            QuizFacade::delete($quiz);

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
