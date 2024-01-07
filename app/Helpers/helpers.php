<?php

use App\Http\Constants\Constant;
use App\Models\Course;
use App\Models\User;

/**
 * @param $data
 * @param $count
 * @param string $message
 * @param int $status
 * @return \Illuminate\Http\JsonResponse
 */
function successResponse($data = [], int $count = 0, string $message = '', int $status = 200): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'message' => $message,
        'success' => true,
        'data' => $data,
        'count' => $count,
        'status' => $status
    ], $status);
}

/**
 * @param $message
 * @param $status
 * @return \Illuminate\Http\JsonResponse
 */
function failResponse($message, $status = 500): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'message' => $message,
        'success' => false,
        'status' => $status,
    ], $status);
}

/**
 * @param $mime_type
 * @return string
 */
function mediaType($mime_type): string
{
    if($mime_type == 'video/mp4' || $mime_type == 'video/mpeg' || $mime_type == 'video/webm' || $mime_type == 'video/3gpp')
    {
        return 'videos';
    }
    if($mime_type == 'image/jpeg' || $mime_type == 'image/gif' || $mime_type == 'image/png' || $mime_type == 'image/svg+xml' || $mime_type == 'image/tiff' || $mime_type == 'image/webp')
    {
        return 'images';
    }
    return 'files';
}


function userHasAccess(User $user, Course $course)
{
    $has_course = ! $user->subscriptions->where('status', Constant::SUBSCRIPTION_STATUS['فعال'])->pluck('courses')->flatten()->where('id', $course->id)->isEmpty();
    return $has_course;
    // return $user->hasRole('super-admin') || $has_course;
}
