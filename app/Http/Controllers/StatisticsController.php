<?php

namespace App\Http\Controllers;

use App\Models\CategoryVisit;
use App\Models\Clicks;
use App\Models\Items;
use App\Models\ItemsVisit;
use App\Models\Order;
use App\Models\UserVisits;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StatisticsController extends Controller
{
    public function index($key="")
    {

        $date_arr = [];
        $user_visits_arr = [];
        $total_clicks_arr = [];
        $orders_arr = [];
        $today = Carbon::now();

        if($key == 'this_week')
        {
            $month = Carbon::now()->startOfWeek();
        }
        elseif($key == 'last_week')
        {
            $month = Carbon::now()->subWeek();
        }
        elseif($key == 'last_month')
        {
            $month = Carbon::now()->subMonth();
        }
        elseif($key == 'last_six_month')
        {
            $month = Carbon::now()->subMonth(6);
        }
        elseif($key == 'last_year')
        {
            $month = Carbon::now()->subYear();
        }
        elseif($key == 'lifetime')
        {
            $month = Carbon::create('2023', '09', '15');
        }
        else
        {
            $month = Carbon::now()->startOfWeek();
        }

        $month_array = CarbonPeriod::create($month, $today);

        if(count($month_array) > 0)
        {
            foreach($month_array as $dateval)
            {
                $date_arr[] = $dateval->format('d-m-Y');
                $user_visits = UserVisits::whereDate('created_at','=',$dateval->format('Y-m-d'))->count();
                $user_visits_arr[$dateval->format('d-m-Y')] = $user_visits;
                $clicks = Clicks::whereDate('created_at','=',$dateval->format('Y-m-d'))->first();
                $orders = Order::whereDate('created_at','=',$dateval->format('Y-m-d'))->count();
                $orders_arr[$dateval->format('d-m-Y')] = $orders;
                $total_clicks_arr[] = isset($clicks['total_clicks']) ? $clicks['total_clicks'] : '';
            };
        }

        // Most 5 Visited Category
        $data['category_visit'] = CategoryVisit::with(['category'])->orderByRaw("CAST(total_clicks as UNSIGNED) DESC")->limit(5)->get();

        // most visited Item
        $data['items_visit'] = ItemsVisit::with(['item'])->orderByRaw("CAST(total_clicks as UNSIGNED) DESC")->limit(5)->get();

        // Max Rated Items
        // $data['max_rated_items'] = Items::withCount('ratings')->withAvg('ratings', 'rating')->orderByDesc('ratings_count')->orderByDesc('ratings_avg_rating')->where('published',1)->limit(5)->get();
        $data['max_rated_items'] = Items::withCount('ratings')->withAvg('ratings', 'rating')->orderByDesc('ratings_avg_rating')->where('published',1)->limit(5)->get();

        // Low Rated Items
        $data['low_rated_items'] = Items::withCount('ratings')->withAvg('ratings', 'rating')->orderBy('ratings_avg_rating')->where('published',1)->limit(5)->get();

        $data['current_key'] = $key;
        $data['date_array'] = $date_arr;
        $data['user_visits_array'] = $user_visits_arr;
        $data['orders_arr'] = $orders_arr;
        $data['total_clicks_array'] = $total_clicks_arr;

        return view('client.statistics.statistics',$data);
    }

}
