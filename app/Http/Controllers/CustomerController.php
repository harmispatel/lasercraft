<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Get All Customers
    function index()
    {
        $customers = User::where('user_type',3)->get();
        return view('client.customers.customers',compact(['customers']));
    }

    // Function for Change Customers Status
    public function changeStatus(Request $request)
    {
        // Customer ID & Status
        $customer_id = $request->id;
        $status = $request->status;

        try
        {
            $customer = User::find($customer_id);
            $customer->status = $status;
            $customer->update();

            return response()->json([
                'success' => 1,
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
            ]);
        }
    }

    // Function for Delete Customer
    function destroy(Request $request)
    {
        try
        {
            $customer_id = $request->id;
            $customer = User::find($customer_id);

            if($customer)
            {
                $orders_ids = Order::where('user_id',$customer_id)->pluck('id');

                if(count($orders_ids) > 0)
                {
                    OrderItems::whereIn('order_id',$orders_ids)->delete();
                    Order::whereIn('id',$orders_ids)->delete();
                }

                $customer_image = (isset($customer->image)) ? $customer->image : '';

                if(!empty($customer_image) && file_exists('public/admin_uploads/users/'.$customer_image))
                {
                    unlink('public/admin_uploads/users/'.$customer_image);
                }

                User::where('id',$customer_id)->delete();

                return response()->json([
                    'success' => 1,
                    'message' => 'Customer has been Removed SuccessFully....',
                ]);
            }

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
