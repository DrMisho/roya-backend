<?php

namespace App\Services;

use App\Http\Constants\Constant;
use App\Services\Service;
use Illuminate\Database\Eloquent\Builder;
use Packages\Auth\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Collection;

class UserService extends Service
{
    public function __construct()
    {
        $this->model = new \App\Models\User();
    }

    public function queryResult(array $array = []): Builder
    {
        $query = $this->model::query();


        $query->when($array['id'] ?? false, fn($query, $id) =>
            $query->where('id', $id)
        );

        $query->when($array['username'] ?? false, fn($query, $username) =>
            $query->where('username', 'like', '%' . $username . '%')
        );

        $query->when($array['phone_number'] ?? false, fn($query, $phone_number) =>
            $query->where('phone_number', $phone_number)
        );

        $query->when($array['search'] ?? false, fn($query, $search) =>
            $query->where(function($query) use($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', $search);
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

    /**
     * @param $grant_type
     * @param $credentials
     * @return \Illuminate\Support\Collection|string
     */
    public function proxy($grant_type, $credentials){
        $url = env('APP_URL_AUTH') . '/oauth/token';
        $data = [
            'grant_type' => $grant_type,
            'client_id' => config('passport.personal_access_client.id'),
            'client_secret' => config('passport.personal_access_client.secret'),
            'scope' => '',
        ];
        $data = array_merge($data, $credentials);
        try{
        $response = Http::asForm()->post($url, $data)->collect();
        return $response;
    } catch (\Exception $e) {
        return $e->getMessage();
    }
    }
}
