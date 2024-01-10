<?php

namespace App\Http\Controllers;

use App\Http\Constants\Constant;
use App\Http\Resources\CobonResource;
use App\Models\Cobon;
use CobonFacade;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CobonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request()->all();
        try
        {
            $cobons = CobonFacade::getList($query);
            $count = CobonFacade::getCount($query);

            return successResponse(CobonResource::collection($cobons), $count);
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
            'number' => 'required|numeric',
            'package_id' => 'required|numeric',
        ]);

        try
        {
            for($i = 0; $i < $data['number']; $i++)
                $cobon = CobonFacade::store($data);
            $count = CobonFacade::getCount();


            DB::commit();
            return successResponse(count: $count, message: "تم الإنشاء بنجاح", status: 201);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    public function checkCobon(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cobon' => 'required|string',
        ]);
        try
        {
            $cobon = $data['cobon'];

            $cobon = Cobon::query()->where('cobon', $cobon)->first();

            if(is_null($cobon))
            {
                return failResponse('هذا الكود غير موجود', 400);
            }

            if($cobon->status == Constant::COBON_STATUS['فعال'])
            {
                return failResponse('هذا الكود قيد الاستخدام', 400);
            }

            if($cobon->status == Constant::COBON_STATUS['منتهي'])
            {
                return failResponse('هذا الكود منتهي الصلاحية', 400);
            }

            if($cobon->package_id == Constant::PACKAGES['فصلي'])
                return successResponse(['status' => 1] ,message: 'هذا الكود فعال وهو كود فصلي');

            return successResponse(['status' => 2], message: 'هذا الكود فعال وهو كود لمادة واحدة فقط');
        }
        catch(Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }
}
