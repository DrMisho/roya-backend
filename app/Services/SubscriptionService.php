<?php

namespace App\Services;

use App\Http\Constants\Constant;
use App\Services\Service;
use Illuminate\Database\Eloquent\Builder;
use Packages\Auth\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionService extends Service
{
    public function __construct()
    {
        $this->model = new \App\Models\Subscription();
    }

    public function queryResult(array $array = []): Builder
    {
        $query = $this->model::query();


        $query->when($array['id'] ?? false, fn($query, $id) =>
            $query->where('id', $id)
        );

        $query->when($array['user_id'] ?? false, fn($query, $user_id) =>
            $query->where('user_id', $user_id)
        );

        $query->when($array['search'] ?? false, fn($query, $search) =>
            $query->where(function($query) use($search) {

            })
            );

        if($array['page'] ?? false)
        {
            $query->paginate($array['limit'] ?? config('app.pagination_limit'));
        }
        else
        {
            if(! $this->count)
            $query->paginate(config('app.pagination_limit'));
        }
        return $query;
    }

}
