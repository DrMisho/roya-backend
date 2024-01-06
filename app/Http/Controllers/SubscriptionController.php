<?php

namespace App\Http\Controllers;

use App\Http\Constants\Constant;
use App\Http\Resources\SubscriptionResource;
use App\Models\Course;
use App\Models\Subscription;
use Arr;
use Carbon\Carbon;
use CourseFacade;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SubscriptionFacade;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $query = request()->all();
        try
        {
            $subscriptions = SubscriptionFacade::getList($query);
            $count = SubscriptionFacade::getCount($query);

            return successResponse(SubscriptionResource::collection($subscriptions), $count);
        }
        catch(Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    public function changeStatus(Subscription $subscription, Request $request): JsonResponse
    {
        DB::beginTransaction();
        $data = $request->validate([
            'status' => 'required|in:0,1,2'
        ]);
        try
        {
            $subscription = SubscriptionFacade::edit($data, $subscription);
            $count = SubscriptionFacade::getCount();

            if($data['status'] == Constant::SUBSCRIPTION_STATUS['فعال'])
            {
                // flutter notification

                SubscriptionFacade::edit([
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addMonth(5)
                ], $subscription);
            }

            DB::commit();
            return successResponse(new SubscriptionResource($subscription), $count, 'تم التعديل بنجاح');
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $data = $request->validate([
            'package_id' => 'required|numeric',
            'course_id' => 'required_if:package_id,2|numeric',
            'academic_year_id' => 'required_if:package_id,1|numeric',
            'semester_id' => 'required_if:package_id,1|numeric',
            'force' => 'nullable|boolean',
        ]);

        try
        {
            $data['user_id'] = auth()->id();

            if($data['package_id'] == Constant::PACKAGES['فصلي'])
            {
                $data['courses'] = Course::query()
                    ->where('academic_year_id', $data['academic_year_id'])
                    ->where('semester_id', $data['semester_id'])
                    ->get()
                    ?->pluck('id');
            }
            else
                $data['courses'] = collect($data['course_id']);

            $subscriptions = auth()
                ->user()
                ->subscriptions
                ->whereIn('status', [Constant::SUBSCRIPTION_STATUS['معلق'], Constant::SUBSCRIPTION_STATUS['فعال']])
                ->pluck('courses')
                ->flatten()
                ->whereIn('id', $data['courses']);

            $is_forces = $data['force']?? false == '1' ? true : false;

            if( (! $subscriptions->isEmpty()) && (! $is_forces ) )
                return failResponse('انت مشترك بشكل مسبق بهذه المواد', 400);

            if($is_forces)
            {
                auth()
                ->user()
                ->subscriptions()
                ->whereIn('status', [Constant::SUBSCRIPTION_STATUS['معلق'], Constant::SUBSCRIPTION_STATUS['فعال']])
                ->whereHas('courses', function($query) use($data) {
                    $query->whereIn('courses.id', $data['courses']);
                })->update(['status' => Constant::SUBSCRIPTION_STATUS['منتهي']]);

            }

            $subscription = SubscriptionFacade::store($data);
            $count = SubscriptionFacade::getCount();

            $subscription->courses()->sync($data['courses']);

            DB::commit();
            return successResponse(new SubscriptionResource($subscription), $count, "تم الإنشاء بنجاح", 201);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    public function show(Subscription $subscription): JsonResponse
    {
        try
        {
            $count = SubscriptionFacade::getCount();

            return successResponse(new SubscriptionResource($subscription), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    public function mySubscription(): JsonResponse
    {
        try
        {
            $subscriptions = auth()->user()->subscriptions;
            $count = SubscriptionFacade::getCount(['user_id' => auth()->id()]);

            return successResponse(SubscriptionResource::collection($subscriptions), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }
}
