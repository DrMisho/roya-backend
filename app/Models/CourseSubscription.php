<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\CourseSubscription
 *
 * @property int $id
 * @property int|null $course_id
 * @property int|null $subscription_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSubscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CourseSubscription extends Pivot
{
    
}
