<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAreas;
use App\Models\Order;
use App\Models\OrderSetting;
use App\Models\UserShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Magarrent\LaravelCurrencyFormatter\Facades\Currency;
use App\Exports\OrdersHistoryExport;

class OrderController extends Controller
{
    // Function for Display Client Orders
    public function index()
    {
        $data['orders'] = Order::whereIn('order_status',['pending','accepted'])->orderBy('id','DESC')->get();

        return view('client.orders.orders',$data);
    }


    // Function for Get newly created order
    public function getNewOrders()
    {
        $html = '';

        $shop_settings = getClientSettings();
        // Shop Currency
        $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'EUR';

        // Order Settings
        $order_setting = getOrderSettings();
        $auto_print = (isset($order_setting['auto_print']) && !empty($order_setting['auto_print'])) ? $order_setting['auto_print'] : 0;
        $enable_print = (isset($order_setting['enable_print']) && !empty($order_setting['enable_print'])) ? $order_setting['enable_print'] : 0;

        // Orders
        $orders = Order::whereIn('order_status',['pending','accepted'])->orderBy('id','DESC')->get();

        if(count($orders) > 0)
        {
            foreach($orders as $order)
            {
                $discount_type = (isset($order->discount_type) && !empty($order->discount_type)) ? $order->discount_type : 'percentage';

                $html .= '<div class="order">';
                    $html .= '<div class="order-btn d-flex align-items-center justify-content-end">';
                        $html .= '<div class="d-flex align-items-center flex-wrap">'.__('Estimated time of arrival').' <input type="number" onchange="changeEstimatedTime(this)" name="estimated_time" id="estimated_time" value="'.$order->estimated_time.'" class="form-control mx-1 estimated_time" style="width: 100px!important" ord-id="'.$order->id.'"';
                        if($order->order_status == 'accepted')
                        {
                            $html .= 'disabled';
                        }
                        else
                        {
                            $html .= '';
                        }
                        $html .= '> '.__('Minutes').'.</div>';

                        if($order->order_status == 'pending')
                        {
                            $html .= '<a class="btn btn-sm btn-primary ms-3" onclick="acceptOrder('.$order->id.')"><i class="bi bi-check-circle" data-bs-toggle="tooltip" title="Accept"></i> '.__('Accept').'</a>';
                            $html .= '<a class="btn btn-sm btn-danger ms-3" onclick="rejectOrder('.$order->id.')"><i class="bi bi-x-circle" data-bs-toggle="tooltip" title="Reject"></i> '.__('Reject').'</a>';
                        }
                        elseif($order->order_status == 'accepted')
                        {
                            $html .= '<a class="btn btn-sm btn-success ms-3" onclick="finalizedOrder('.$order->id.')"><i class="bi bi-check-circle" data-bs-toggle="tooltip" title="Complete"></i> '.__('Finalize').'</a>';
                        }

                        if($enable_print == 1)
                        {
                            $html .= '<a class="btn btn-sm btn-primary ms-3" onclick="printReceipt('.$order->id .')"><i class="bi bi-printer"></i> Print</a>';
                        }

                    $html .= '</div>';

                    $html .= '<div class="order-info">';
                        $html .= '<ul>';
                            $html .= '<li><strong>#'.$order->id.'</strong></li>';
                            $html .= '<li><strong>'.__('Order Date').' : </strong>'.date('d-m-Y h:i:s',strtotime($order->created_at)).'</li>';
                            $html .= '<li><strong>'.__('Order Type').' : </strong>'.$order->checkout_type.'</li>';
                            $html .= '<li><strong>'.__('Payment Method').' : </strong>'.$order->payment_method.'</li>';

                            if($order->checkout_type == 'takeaway')
                            {
                                $html .= '<li><strong>'.__('Customer').' : </strong>'.$order->firstname.' '.$order->lastname.'</li>';
                                $html .= '<li><strong>'.__('Telephone').' : </strong> '.$order->phone.'</li>';
                                $html .= '<li><strong>'.__('Email').' : </strong> '.$order->email.'</li>';
                            }
                            elseif($order->checkout_type == 'table_service')
                            {
                                $html .= '<li><strong>'.__('Table No.').' : </strong> '.$order->table.'</li>';
                            }
                            elseif($order->checkout_type == 'office_service')
                            {
                                $html .= '<li><strong>'.__('Building').' : </strong> '.$order->building.'</li>';
                                $html .= '<li><strong>'.__('Office No.').' : </strong> '.$order->office_no.'</li>';
                            }
                            elseif($order->checkout_type == 'room_delivery')
                            {
                                $html .= '<li><strong>'.__('Customer').' : </strong>'.$order->firstname.' '.$order->lastname.'</li>';
                                $html .= '<li><strong>'.__('Room No.').' : </strong> '.$order->room.'</li>';
                                if(!empty($order->delivery_time ))
                                {
                                    $html .= '<li><strong>'.__('Delivery Time').' : </strong> '.$order->delivery_time.'</li>';
                                }
                            }
                            elseif($order->checkout_type == 'delivery')
                            {
                                $html .= '<li><strong>'.__('Customer').' : </strong>'.$order->firstname.' '.$order->lastname.'</li>';
                                $html .= '<li><strong>'.__('Telephone').' : </strong> '.$order->phone.'</li>';
                                $html .= '<li><strong>'.__('Email').' : </strong> '.$order->email.'</li>';
                                $html .= '<li><strong>'.__('Address').' : </strong> '.$order->address.'</li>';
                                $html .= '<li><strong>'.__('Floor').' : </strong> '.$order->floor.'</li>';
                                $html .= '<li><strong>'.__('Door Bell').' : </strong> '.$order->door_bell.'</li>';
                                $html .= '<li><strong>'.__('Google Map').' : </strong> <a href="https://maps.google.com?q='.$order->address.'" target="_blank">Address Link</a></li>';
                                $html .= '<li><strong>'.__('Comments').' : </strong> '.$order->instructions.'</li>';
                            }

                        $html .= '</ul>';
                    $html .= '</div>';

                    $html .= '<hr>';

                    $html .= '<div class="order-info mt-2">';
                        $html .= '<div class="row">';
                            $html .= '<div class="col-md-3">';
                                $html .= '<table class="table">';

                                    $html .= '<tr>';
                                        $html .= '<td><b>'.__('Sub Total').'</b></td>';
                                        $html .= '<td class="text-end">'. Currency::currency($currency)->format($order->order_subtotal).'</td>';
                                    $html .= '</tr>';

                                    if($order->discount_per > 0)
                                    {
                                        $html .= '<tr>';
                                            $html .= '<td><b>'.__('Discount').'</b></td>';
                                            if($order->discount_per == 'fixed')
                                            {
                                                $html .= '<td class="text-end">- '.Currency::currency($currency)->format($order->discount_per).'</td>';
                                            }
                                            else
                                            {
                                                $html .= '<td class="text-end">- '.$order->discount_per.'%</td>';
                                            }
                                        $html .= '</tr>';
                                    }

                                    if($order->cgst > 0 && $order->sgst > 0)
                                    {
                                        $gst_amt = $order->cgst + $order->sgst;
                                        $gst_amt = $order->gst_amount / $gst_amt;

                                        $html .= '<tr>';
                                            $html .= '<td><b>'.__('CGST.').' ('.$order->cgst.'%)</b></td>';
                                            $html .= '<td class="text-end">+ '.Currency::currency($currency)->format($order->cgst * $gst_amt).'</td>';
                                        $html .= '</tr>';

                                        $html .= '<tr>';
                                            $html .= '<td><b>'.__('SGST.').' ('.$order->sgst.'%)</b></td>';
                                            $html .= '<td class="text-end">+ '.Currency::currency($currency)->format($order->sgst * $gst_amt).'</td>';
                                        $html .= '</tr>';
                                    }

                                    $html .= '<tr class="text-end">';
                                        $html .= '<td colspan="2"><b>'.Currency::currency($currency)->format($order->order_total).'</b></td>';
                                    $html .= '</tr>';

                                $html .= '</table>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';

                    $html .= '<hr>';

                    $html .= '<div class="order-items">';
                        $html .= '<div class="row">';
                            if(count($order->order_items) > 0)
                            {
                                $html .= '<div class="col-md-8">';
                                    $html .= '<table class="table">';
                                        foreach ($order->order_items as $ord_item)
                                        {
                                            $sub_total = ( $ord_item['sub_total'] / $ord_item['item_qty']);
                                            $option = unserialize($ord_item['options']);

                                            $html .= '<tr>';
                                                $html .= '<td>';
                                                    $html .= '<b>'.$ord_item['item_qty'].' x '.$ord_item['item_name'].'</b>';
                                                    if(!empty($option))
                                                    {
                                                        $html .= '<br> '.implode(', ',$option);
                                                    }
                                                $html .= '</td>';
                                                $html .= '<td width="25%" class="text-end">'.Currency::currency($currency)->format($sub_total).'</td>';
                                                $html .= '<td width="25%" class="text-end">'.$ord_item['sub_total_text'].'</td>';
                                            $html .= '</tr>';
                                        }
                                    $html .= '</table>';
                                $html .= '</div>';
                            }
                        $html .= '</div>';
                    $html .= '</div>';

                $html .= '</div>';
            }
        }
        else
        {
            $html .= '<div class="row">';
                $html .= '<div class="col-md-12 text-center">';
                    $html .= '<h3>Orders Not Available</h3>';
                $html .= '</div>';
            $html .= '</div>';
        }

        return response()->json([
            'success' => 1,
            'data' => $html,
        ]);
    }


    // Function for Display Client Orders History
    public function ordersHistory(Request $request)
    {
        $data['payment_method'] = '';
        $data['status_filter'] = '';
        $data['day_filter'] = '';
        $data['total_text'] = 'Total Amount';
        $data['total'] = 0.00;
        $data['start_date'] = Carbon::now();
        $data['end_date'] = Carbon::now();
        $data['StartDate'] = '';
        $data['EndDate'] = '';

        if($request->isMethod('get'))
        {
            $data['orders'] = Order::orderBy('id','desc')->get();
            $data['total'] = Order::sum('order_total');
        }
        else
        {
            $orders = Order::query();
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $data['payment_method'] = (isset($request->filter_by_payment_method)) ? $request->filter_by_payment_method : '';
            $data['status_filter'] = (isset($request->filter_by_status)) ? $request->filter_by_status : '';

            // Payment Method Filter
            if(!empty($data['payment_method']))
            {
                $orders = $orders->where('payment_method',$data['payment_method']);
                $data['total'] = $orders->sum('order_total');
            }
            else
            {
                $data['total'] = $orders->sum('order_total');
            }

            // Status Filter
            if(!empty($data['status_filter']))
            {
                $orders = $orders->where('order_status',$data['status_filter']);
                $data['total'] = $orders->sum('order_total');
            }
            else
            {
                $data['total'] = $orders->sum('order_total');
            }

            if(!empty($start_date) && !empty($end_date))
            {
                $data['start_date'] = $start_date;
                $data['StartDate'] = $start_date;
                $data['end_date'] = $end_date;
                $data['EndDate'] = $end_date;

                $orders = $orders->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
                $data['total'] = $orders->sum('order_total');
                $data['orders'] = $orders->get();
            }
            else
            {

                // Day Filter
                $data['day_filter'] = (isset($request->filter_by_day)) ? $request->filter_by_day : '';
                if(!empty($data['day_filter']))
                {
                    if($data['day_filter'] == 'today')
                    {
                        $today = Carbon::today();
                        $orders = $orders->whereDate('created_at', $today);
                        $data['total_text'] = "Today's Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                    elseif($data['day_filter'] == 'this_week')
                    {
                        $startOfWeek = Carbon::now()->startOfWeek();
                        $endOfWeek = Carbon::now()->endOfWeek();
                        $orders = $orders->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                        $data['total_text'] = "This Week Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                    elseif($data['day_filter'] == 'last_week')
                    {
                        $startOfWeek = Carbon::now()->subWeek()->startOfWeek();
                        $endOfWeek = Carbon::now()->subWeek()->endOfWeek();
                        $orders = $orders->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                        $data['total_text'] = "Last Week Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                    elseif($data['day_filter'] == 'this_month')
                    {
                        $currentMonth = Carbon::now()->format('Y-m');
                        $orders = $orders->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth]);
                        $data['total_text'] = "This Month Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                    elseif($data['day_filter'] == 'last_month')
                    {
                        $startDate = Carbon::now()->subMonth()->startOfMonth();
                        $endDate = Carbon::now()->subMonth()->endOfMonth();
                        $orders = $orders->whereBetween('created_at', [$startDate, $endDate]);
                        $data['total_text'] = "Last Month Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                    elseif($data['day_filter'] == 'last_six_month')
                    {
                        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
                        $endDate = Carbon::now()->subMonth()->endOfMonth();
                        $orders = $orders->whereBetween('created_at', [$startDate, $endDate]);
                        $data['total_text'] = "Last Six Months Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                    elseif($data['day_filter'] == 'this_year')
                    {
                        $startOfYear = Carbon::now()->startOfYear();
                        $endOfYear = Carbon::now()->endOfYear();
                        $orders = $orders->whereBetween('created_at', [$startOfYear, $endOfYear]);
                        $data['total_text'] = "This Year Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                    elseif($data['day_filter'] == 'last_year')
                    {
                        $startOfYear = Carbon::now()->subYear()->startOfYear();
                        $endOfYear = Carbon::now()->subYear()->endOfYear();
                        $orders = $orders->whereBetween('created_at', [$startOfYear, $endOfYear]);
                        $data['total_text'] = "Last Year Total Amount";
                        $data['total'] = $orders->sum('order_total');
                    }
                }
            }

            $data['orders'] = $orders->orderBy('id','desc')->get();
        }

        return view('client.orders.orders_history',$data);
    }



    // Function for Export Order History
    public function exportOrderHistory(Request $request)
    {
        $filter_by_day = $request->day_value;

        try
        {
            $orders = Order::query();

            if($filter_by_day == 'today')
            {
                $today = Carbon::today();
                $orders = $orders->whereDate('created_at', $today);
            }
            elseif($filter_by_day == 'this_week')
            {
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();
                $orders = $orders->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
            elseif($filter_by_day == 'last_week')
            {
                $startOfWeek = Carbon::now()->subWeek()->startOfWeek();
                $endOfWeek = Carbon::now()->subWeek()->endOfWeek();
                $orders = $orders->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
            elseif($filter_by_day == 'this_month')
            {
                $currentMonth = Carbon::now()->format('Y-m');
                $orders = $orders->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth]);
            }
            elseif($filter_by_day == 'last_month')
            {
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $orders = $orders->whereBetween('created_at', [$startDate, $endDate]);
            }
            elseif($filter_by_day == 'last_six_month')
            {
                $startDate = Carbon::now()->subMonths(6)->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $orders = $orders->whereBetween('created_at', [$startDate, $endDate]);
            }
            elseif($filter_by_day == 'this_year')
            {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();
                $orders = $orders->whereBetween('created_at', [$startOfYear, $endOfYear]);
            }
            elseif($filter_by_day == 'last_year')
            {
                $startOfYear = Carbon::now()->subYear()->startOfYear();
                $endOfYear = Carbon::now()->subYear()->endOfYear();
                $orders = $orders->whereBetween('created_at', [$startOfYear, $endOfYear]);
            }

            $orders = $orders->get();

            if(count($orders) > 0)
            {
                return Excel::download(new OrdersHistoryExport($orders,$shop_id),'order_history.xlsx');
            }
            else
            {
                return redirect()->back()->with('error','Orders not Found');
            }

        }
        catch (\Throwable $th)
        {
            return redirect()->back()->with('error','Internal Server Error!');
        }

    }


    // function for view OrderSettings
    public function OrderSettings()
    {
        $data['order_settings'] = getOrderSettings();
        $data['deliveryAreas'] = DeliveryAreas::get();

        return view('client.orders.order_settings',$data);
    }


    // Function for Update Order Settings
    public function UpdateOrderSettings(Request $request)
    {
        $all_data['delivery'] = (isset($request->delivery)) ? $request->delivery : 0;
        $all_data['takeaway'] = (isset($request->takeaway)) ? $request->takeaway : 0;
        $all_data['scheduler_active'] = (isset($request->scheduler_active)) ? $request->scheduler_active : 0;
        $all_data['min_amount_for_delivery'] = (isset($request->min_amount_for_delivery)) ? $request->min_amount_for_delivery : '';
        $all_data['discount_percentage'] = (isset($request->discount_percentage)) ? $request->discount_percentage : '';
        $all_data['order_arrival_minutes'] = (isset($request->order_arrival_minutes)) ? $request->order_arrival_minutes : 30;
        $all_data['schedule_array'] = $request->schedule_array;
        $all_data['discount_type'] = $request->discount_type;
        $all_data['play_sound'] = (isset($request->play_sound)) ? $request->play_sound : 0;
        $all_data['notification_sound'] = (isset($request->notification_sound)) ? $request->notification_sound : 'buzzer-01.mp3';

        try
        {
            // Insert or Update Settings
            foreach($all_data as $key => $value)
            {
                $query = OrderSetting::where('key',$key)->first();
                $setting_id = isset($query->id) ? $query->id : '';

                if (!empty($setting_id) || $setting_id != '')  // Update
                {
                    $settings = OrderSetting::find($setting_id);
                    $settings->value = $value;
                    $settings->update();
                }
                else // Insert
                {
                    $settings = new OrderSetting();
                    $settings->key = $key;
                    $settings->value = $value;
                    $settings->save();
                }
            }

            // Insert Delivery Zones Area
            $delivery_zones = (isset($request->new_coordinates) && !empty($request->new_coordinates)) ? json_decode($request->new_coordinates,true) : [];

            if(count($delivery_zones) > 0)
            {
                foreach($delivery_zones as $delivery_zone)
                {
                    $polygon = serialize($delivery_zone);

                    $delivery_area = new DeliveryAreas();
                    $delivery_area->coordinates = $polygon;
                    $delivery_area->save();
                }
            }

            return response()->json([
                'success' => 1,
                'message' => 'Setting has been Updated SuccessFully...',
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


    // Function for Clear Delivery Range Settings
    public function clearDeliveryRangeSettings()
    {
        DeliveryAreas::deleteAll();

        return redirect()->route('order.settings')->with('success',"Setting has been Updated SuccessFully..");

    }


    // Function for Change Order Estimated Time
    public function changeOrderEstimate(Request $request)
    {
        $order_id = $request->order_id;
        $estimated_time = $request->estimate_time;
        if($estimated_time == '' || $estimated_time == 0 || $estimated_time < 0)
        {
            $estimated_time = '30';
        }

        try
        {
            $order = Order::find($order_id);
            $order->estimated_time = $estimated_time;
            $order->update();

            return response()->json([
                'success' => 1,
                'message' => 'Time has been Changed SuccessFully...',
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


    // Function for Accpeting Order
    public function acceptOrder(Request $request)
    {
        $order_id = $request->order_id;
        try
        {
            // Shop ID
            $shop_id = (isset(Auth::user()->hasOneShop->shop['id'])) ? Auth::user()->hasOneShop->shop['id'] : '';
            $shop_name = isset(Auth::user()->hasOneShop->shop['name']) ? Auth::user()->hasOneShop->shop['name'] : '';
            $shop_url = (isset(Auth::user()->hasOneShop->shop['shop_slug'])) ? Auth::user()->hasOneShop->shop['shop_slug'] : '';
            $shop_slug = (isset(Auth::user()->hasOneShop->shop['shop_slug'])) ? Auth::user()->hasOneShop->shop['shop_slug'] : '';
            $shop_url = asset($shop_url);
            $shop_name = '<a href="'.$shop_url.'">'.$shop_name.'</a>';
            $shop_logo = (isset(Auth::user()->hasOneShop->shop['logo'])) ? Auth::user()->hasOneShop->shop['logo'] : '';
            $shop_logo = '<img src="'.$shop_logo.'" width="200">';

            // Update Order Status
            $order = Order::find($order_id);
            $order->order_status = 'accepted';
            $order->is_new = 0;
            $order->update();

            // Get Shop Settings
            $shop_settings = getClientSettings($shop_id);
            $orders_mail_form_customer = (isset($shop_settings['orders_mail_form_customer'])) ? $shop_settings['orders_mail_form_customer'] : '';

            // Shop Currency
            $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'EUR';

            // Get Contact Emails
            $shop_user = UserShop::with(['user'])->where('shop_id',$shop_id)->first();
            $contact_emails = (isset($shop_user->user['contact_emails']) && !empty($shop_user->user['contact_emails'])) ? unserialize($shop_user->user['contact_emails']) : '';

            // Sent Mail to Customer
            if($order->id)
            {
                $order_items = (isset($order->order_items) && count($order->order_items) > 0) ? $order->order_items : [];
                $discount_type = (isset($order->discount_type) && !empty($order->discount_type)) ? $order->discount_type : 'percentage';

                $checkout_type =  (isset($order->checkout_type)) ? $order->checkout_type : '';
                $payment_method =  (isset($order->payment_method)) ? $order->payment_method : '';

                $from_email = (isset($order->email)) ? $order->email : '';

                if($checkout_type == 'takeaway' || $checkout_type == 'delivery')
                {
                    if(!empty($from_email) && count($contact_emails) > 0 && !empty($orders_mail_form_customer))
                    {
                        $to = $from_email;
                        $from = $contact_emails[0];
                        $subject = "Order Placed";
                        $fname = (isset($order->firstname)) ? $order->firstname : '';
                        $lname = (isset($order->lastname)) ? $order->lastname : '';
                        $estimated_time = (isset($order->estimated_time)) ? $order->estimated_time : '';

                        $message = $orders_mail_form_customer;
                        $message = str_replace('{shop_logo}',$shop_logo,$message);
                        $message = str_replace('{shop_name}',$shop_name,$message);
                        $message = str_replace('{firstname}',$fname,$message);
                        $message = str_replace('{lastname}',$lname,$message);
                        $message = str_replace('{order_id}',$order->id,$message);
                        $message = str_replace('{order_type}',$checkout_type,$message);
                        $message = str_replace('{payment_method}',$payment_method,$message);
                        $message = str_replace('{order_status}','Accepted',$message);
                        $message = str_replace('{estimated_time}',$estimated_time,$message);

                        // Order Items
                        $order_html  = "";
                        $order_html .= '<div>';
                            $order_html .= '<table style="width:100%; border:1px solid gray;border-collapse: collapse;">';
                                $order_html .= '<thead style="background:lightgray; color:white">';
                                    $order_html .= '<tr style="text-transform: uppercase!important;    font-weight: 700!important;">';
                                        $order_html .= '<th style="text-align: left!important;width: 60%;padding:10px">Item</th>';
                                        $order_html .= '<th style="text-align: center!important;padding:10px">Qty.</th>';
                                        $order_html .= '<th style="text-align: right!important;padding:10px">Item Total</th>';
                                    $order_html .= '</tr>';
                                $order_html .= '</thead>';
                                $order_html .= '<tbody style="font-weight: 600!important;">';

                                    if(count($order_items) > 0)
                                    {
                                        foreach($order_items as $order_item)
                                        {
                                            $item_dt = itemDetails($order_item['item_id']);
                                            $item_image = (isset($item_dt['image']) && !empty($item_dt['image']) && file_exists('public/client_uploads/shops/'.$shop_slug.'/items/'.$item_dt['image'])) ? asset('public/client_uploads/shops/'.$shop_slug.'/items/'.$item_dt['image']) : asset('public/client_images/not-found/no_image_1.jpg');
                                            $options_array = (isset($order_item['options']) && !empty($order_item['options'])) ? unserialize($order_item['options']) : '';
                                            if(count($options_array) > 0)
                                            {
                                                $options_array = implode(', ',$options_array);
                                            }

                                            $order_html .= '<tr>';

                                                $order_html .= '<td style="text-align: left!important;padding:10px; border-bottom:1px solid gray;">';
                                                    $order_html .= '<div style="align-items: center!important;display: flex!important;">';
                                                        $order_html .= '<a style="display: inline-block;
                                                        flex-shrink: 0;position: relative;border-radius: 0.75rem;">';
                                                            $order_html .= '<span style="width: 50px;
                                                            height: 50px;display: flex;
                                                            align-items: center;
                                                            justify-content: center;
                                                            font-weight: 500;background-repeat: no-repeat;
                                                            background-position: center center;
                                                            background-size: cover;
                                                            border-radius: 0.75rem; background-image:url('.$item_image.')"></span>';
                                                        $order_html .= '</a>';
                                                        $order_html .= '<div style="display: block;    margin-left: 3rem!important;">';
                                                            $order_html .= '<a style="font-weight: 700!important;color: #7e8299;
                                                            ">'.$order_item->item_name.'</a>';

                                                            if(!empty($options_array))
                                                            {
                                                                $order_html .= '<div style="color: #a19e9e;display: block;">'.$options_array.'</div>';
                                                            }
                                                            else
                                                            {
                                                                $order_html .= '<div style="color: #a19e9e;display: block;"></div>';
                                                            }

                                                        $order_html .= '</div>';
                                                    $order_html .= '</div>';
                                                $order_html .= '</td>';

                                                $order_html .= '<td style="text-align: center!important;padding:10px; border-bottom:1px solid gray;">';
                                                    $order_html .= $order_item['item_qty'];
                                                $order_html .= '</td>';

                                                $order_html .= '<td style="text-align: right!important;padding:10px; border-bottom:1px solid gray;">';
                                                    $order_html .= Currency::currency($currency)->format($order_item['sub_total']);
                                                $order_html .= '</td>';

                                            $order_html .= '</tr>';
                                        }
                                    }

                                $order_html .= '</tbody>';
                            $order_html .= '</table>';
                        $order_html .= '</div>';
                        $message = str_replace('{items}',$order_html,$message);

                        // Order Total
                        $order_total_html = "";
                                $order_total_html .= '<div>';
                                    $order_total_html .= '<table style="width:50%; border:1px solid gray;border-collapse: collapse;">';
                                        $order_total_html .= '<tbody style="font-weight: 700!important;">';
                                            $order_total_html .= '<tr>';
                                                $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">Sub Total : </td>';
                                                $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">'.Currency::currency($currency)->format($order->order_subtotal).'</td>';
                                            $order_total_html .= '</tr>';

                                            if($order->discount_per > 0)
                                            {
                                                $order_total_html .= '<tr>';
                                                    $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">Discount : </td>';
                                                    if($order->discount_per == 'fixed')
                                                    {
                                                        $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">- '.Currency::currency($currency)->format($order->discount_per).'</td>';
                                                    }
                                                    else
                                                    {
                                                        $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">- '.$order->discount_per.'%</td>';
                                                    }
                                                $order_total_html .= '</tr>';
                                            }

                                            if($order->cgst > 0 && $order->sgst > 0)
                                            {
                                                $gst_amt = $order->cgst + $order->sgst;
                                                $gst_amt = $order->gst_amount / $gst_amt;

                                                $order_total_html .= '<tr>';
                                                    $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">'.__('CGST.').' ('.$order->cgst.'%)</td>';
                                                    $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">+ '.Currency::currency($currency)->format($order->cgst * $gst_amt).'</td>';
                                                $order_total_html .= '</tr>';
                                                $order_total_html .= '<tr>';
                                                    $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">'.__('SGST.').' ('.$order->sgst.'%)</td>';
                                                    $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">+ '.Currency::currency($currency)->format($order->sgst * $gst_amt).'</td>';
                                                $order_total_html .= '</tr>';
                                            }

                                            $order_total_html .= '<tr>';
                                                $order_total_html .= '<td style="padding:10px;">Total : </td>';
                                                $order_total_html .= '<td style="padding:10px;">';
                                                    $order_total_html .= Currency::currency($currency)->format($order->order_total);
                                                $order_total_html .= '</td>';
                                            $order_total_html .= '</tr>';

                                        $order_total_html .= '</tbody>';
                                    $order_total_html .= '</table>';
                                $order_total_html .= '</div>';
                                $message = str_replace('{total}',$order_total_html,$message);

                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                        // More headers
                        $headers .= 'From: <'.$from.'>' . "\r\n";

                        mail($to,$subject,$message,$headers);

                    }
                }
            }

            return response()->json([
                'success' => 1,
                'message' => 'Order has been Accepted SuccessFully...',
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


    // Function for Reject Order
    public function rejectOrder(Request $request)
    {
        $order_id = $request->order_id;
        $reject_reason = $request->reject_reason;
        try
        {
            // Update Order Status
            $order = Order::find($order_id);
            $order->order_status = 'rejected';
            $order->reject_reason = $reject_reason;
            $order->is_new = 0;
            $order->update();

            return response()->json([
                'success' => 1,
                'message' => 'Order has been Rejected SuccessFully...',
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


    // Function for Finalized Order
    public function finalizedOrder(Request $request)
    {
        $order_id = $request->order_id;
        try
        {
            $order = Order::find($order_id);
            $order->order_status = 'completed';
            $order->update();

            return response()->json([
                'success' => 1,
                'message' => 'Order has been Completed SuccessFully...',
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


    // Function for view Order
    public function viewOrder($order_id)
    {
        try
        {
            $order_id = decrypt($order_id);
            $data['order'] = Order::with(['order_items'])->where('id',$order_id)->first();
            return view('client.orders.order_details',$data);
        }
        catch (\Throwable $th)
        {
            return redirect()->route('client.orders')->with('error',"Internal Server Error!");
        }
    }


    // Function for Set Delivery Address in Session
    public function setDeliveryAddress(Request $request)
    {
        $lat = $request->latitude;
        $lng = $request->longitude;
        $address = $request->address;

        try
        {
            session()->put('cust_lat',$lat);
            session()->put('cust_long',$lng);
            session()->put('cust_address',$address);
            session()->save();

            $delivey_avaialbility = checkDeliveryAvilability($lat,$lng);

            return response()->json([
                'success' => 1,
                'message' => 'Address has been set successfully...',
                'available' => $delivey_avaialbility,
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


    // Function for Get Order Notification
    public function orderNotification(Request $request)
    {
        $html = '';
        $new_order_count = Order::where('order_status','pending')->where('is_new',1)->count();

        if($new_order_count > 0)
        {
            $html .= 'You Have '.$new_order_count.' New Orders';
            $html .= '<a href="'.route('client.orders').'"><span class="badge rounded-pill bg-primary p-2 ms-2">View All</span></a>';
        }
        else
        {
            $html .= 'You Have 0 New Orders';
            $html .= '<a href="'.route('client.orders').'"><span class="badge rounded-pill bg-primary p-2 ms-2">View All</span></a>';
        }


        return response()->json([
            'success' => 1,
            'data' => $html,
            'count' => $new_order_count,
        ]);
    }

}
