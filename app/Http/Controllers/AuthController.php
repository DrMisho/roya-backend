<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use UserFacade;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param RegisterUserRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try
        {
            DB::beginTransaction();

            $user = UserFacade::store([
                'device' => $request->header('device'),
                'password' => Hash::make($request->password),
                ...$request->except('password'),
            ]);

            $credentials = [
                'username' => $user->username,
                'password' => $request->password,
            ];

            DB::commit();

            $response = UserFacade::proxy('password', $credentials);

            return response()->json($response);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $column = "username";

        if($request->is_phone_number)
            $column = "phone_number";

        $user = User::query()->where($column, $request[$column])->first();

        if ($user) {
            if (Hash::check($request['password'], $user->password)) {
                $credentials = [
                    'username' => $user->username,
                    'password' => $request->password,
                ];

                if(is_null($user->device))
                {
                    $user->device = $request->header('device');
                    $user->save();
                }

                if($request->header('device') != $user->device)
                    return failResponse('لا تستطيع تسجيل الدخول من اكثر من جهاز', 400);

                $response = UserFacade::proxy('password', $credentials);

                return response()->json($response);
            } else {
                return failResponse(trans("message.password_mismatch"), 422);
            }
        } else {
            return failResponse(trans('message.user_does_not_exist'), 422);
        }

    }

    /**
     * Display the specified resource.
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        if (Auth::check()) {
            $user = auth()->user();
            return successResponse(new UserResource($user));

        }
        return failResponse(trans('message.unauthorized'), 401);
    }

    /**
     * Update the specified resource in storage.
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $tokenId = Auth::user()->token()->id;
        $tokenRepository = app(TokenRepository::class);
        try {
            UserFacade::edit(['last_login' => Carbon::now()], auth()->user());
            $refreshTokenRepository = app(RefreshTokenRepository::class);
            $tokenRepository->revokeAccessToken($tokenId);
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

            return successResponse(message: trans('message.logout_successfully'));
        } catch (Exception $e) {
            return failResponse($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $refresh_token = $request->input('refresh_token');
        $credentials = [
            'refresh_token' => $refresh_token,
        ];

        try {
            $response = UserFacade::proxy('refresh_token', $credentials);
            return response()->json($response);

        } catch (Exception $e) {
            return failResponse($e->getMessage());
        }

    }

    public function update(EditUserRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try
        {
            $credentials = $request->validated();

            $user = UserFacade::getSingle($id);

            $user = UserFacade::edit($credentials, $user);

            if (key_exists('image', $request->validated()))
            {
                UserFacade::editMedia($user, $request->file('image'), User::class, $user->id);
            }
            DB::commit();
            return successResponse(new UserResource($user->refresh()), message: trans('message.edited_successfully'));

        }
        catch (Exception $e) {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try
        {
            $credentials = $request->validated();
            $user = auth()->user();

            if (Hash::check($credentials['old_password'], $user->password))
            {
                UserFacade::edit(['password' => Hash::make($credentials['password'])], $user);

                DB::commit();
                return successResponse(new UserResource($user), message: trans('message.password_has_changed'));
            }
            return failResponse(trans('message.your_old_password_is_not_correct'), 400);

        }
        catch (Exception $e) {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }
}
