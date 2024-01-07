<?php

namespace App\Http\Controllers;

use App\Models\User;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UserFacade;

class UserController extends Controller
{
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
