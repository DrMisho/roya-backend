<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use DB;
use Exception;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'UID' => 'nullable|string|max:255',
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        try
        {
            DB::beginTransaction();

            $user = UserFacade::store([
                'password' => Hash::make($data['password']),
                ...$request->except('password'),
            ]);

            $superAdmin = Role::findByName('super-admin');
            $user->assignRole($superAdmin);

            DB::commit();
            return successResponse(new UserResource($user), status: 201);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }
}
