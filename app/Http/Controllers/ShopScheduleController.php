<?php

namespace App\Http\Controllers;

use App\Models\ShopSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopScheduleController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $shop_id = isset(Auth::user()->hasOneShop->shop['id']) ? Auth::user()->hasOneShop->shop['id'] : '';
        $data['shop_schedule'] = ShopSchedule::where('shop_id',$shop_id)->first();

        return view('client.schedule.shop-schedule',$data);
    }



    // Update the specified resource in storage.
    public function updateShopSchedule(Request $request)
    {
        $shop_id = isset(Auth::user()->hasOneShop->shop['id']) ? Auth::user()->hasOneShop->shop['id'] : '';

        $scheduler_active = (isset($request->scheduler_active)) ? $request->scheduler_active : 0;
        $value = $request->schedule_array;

        try
        {
            $old_schedule = ShopSchedule::where('shop_id',$shop_id)->first();
            $schedule_id = (isset($old_schedule['id'])) ? $old_schedule['id'] : '';

            if(!empty($schedule_id))
            {
                $schedule = ShopSchedule::find($schedule_id);
                $schedule->status = $scheduler_active;
                $schedule->value = $value;
                $schedule->update();
            }
            else
            {
                $schedule = new ShopSchedule;
                $schedule->shop_id = $shop_id;
                $schedule->status = $scheduler_active;
                $schedule->value = $value;
                $schedule->save();
            }

            return response()->json([
                'success' => 1,
                'message' => 'Schedule Setting has been Updated SuccessFully..',
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }
    }

}
