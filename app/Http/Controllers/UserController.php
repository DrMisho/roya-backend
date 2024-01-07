<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UserFacade;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $query = request()->all();
        try
        {
            $users = UserFacade::getList($query);
            $count = UserFacade::getCount($query);

            return successResponse(UserResource::collection($users), $count);
        }
        catch (\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    public function resetDevice(User $user): JsonResponse
    {
        DB::beginTransaction();
        try
        {
            $user->device = null;
            $user->save();

            DB::commit();
            return successResponse(message: 'تم التعديل بنجاح');
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }
}
