<?php

    use App\Models\{Category, ClientSettings, DeliveryAreas, Items, Languages,LanguageSettings, OrderSetting, PaymentSettings, ThemeSettings, ShopSchedule};
    use Carbon\Carbon;

    // Get Client's Settings
    function getClientSettings()
    {
        // Keys
        $keys = ([
            'shop_view_header_logo',
            'shop_intro_icon',
            'intro_icon_status',
            'intro_icon_duration',
            'business_name',
            'default_currency',
            'business_telephone',
            'instagram_link',
            'pinterest_link',
            'twitter_link',
            'facebook_link',
            'youtube_link',
            'map_url',
            'homepage_intro',
            'shop_active_theme',
            'delivery_message',
            'orders_mail_form_client',
            'orders_mail_form_customer',
            'check_in_mail_form',
            'pickup_address',
        ]);

        $settings = [];

        foreach($keys as $key)
        {
            $query = ClientSettings::select('value')->where('key',$key)->first();
            $settings[$key] = isset($query->value) ? $query->value : '';
        }

        return $settings;
    }


    // Get Order Settings
    function getOrderSettings()
    {
        // Keys
        $keys = ([
            'delivery',
            'takeaway',
            'scheduler_active',
            'min_amount_for_delivery',
            'discount_percentage',
            'order_arrival_minutes',
            'schedule_array',
            'play_sound',
            'notification_sound',
            'discount_type',
        ]);

        $settings = [];

        foreach($keys as $key)
        {
            $query = OrderSetting::select('value')->where('key',$key)->first();
            $settings[$key] = isset($query->value) ? $query->value : '';
        }

        return $settings;
    }


    // Get Payment Settings
    function getPaymentSettings()
    {
        // Keys
        $keys = [
            'cash',
            'paypal',
            'paypal_mode',
            'paypal_public_key',
            'paypal_private_key',
            // 'upi_payment',
            // 'upi_id',
            // 'payee_name',
            // 'upi_qr',
        ];

        $settings = [];

        foreach($keys as $key)
        {
            $query = PaymentSettings::select('value')->where('key',$key)->first();
            $settings[$key] = isset($query->value) ? $query->value : '';
        }

        return $settings;
    }


    // Get Client's LanguageSettings
    function clientLanguageSettings()
    {
        // Keys
        $keys = ([
            'primary_language',
            'google_translate',
        ]);

        $settings = [];

        foreach($keys as $key)
        {
            $query = LanguageSettings::select('value')->where('key',$key)->first();
            $settings[$key] = isset($query->value) ? $query->value : '';
        }

        return $settings;
    }


    // Get Theme Settings
    function themeSettings($themeID)
    {
        // Keys
        $keys = ([
            'header_color',
            'sticky_header',
            'language_bar_position',
            'logo_position',
            'search_box_position',
            'banner_position',
            'banner_type',
            'banner_slide_button',
            'banner_delay_time',
            'background_color',
            'font_color',
            'label_color',
            'social_media_icon_color',
            'categories_bar_color',
            'menu_bar_font_color',
            'category_title_and_description_color',
            'price_color',
            'item_box_shadow',
            'item_box_shadow_color',
            'item_box_shadow_thickness',
            'item_divider',
            'item_divider_color',
            'item_divider_thickness',
            'item_divider_type',
            'item_divider_position',
            'item_divider_font_color',
            'tag_font_color',
            'tag_label_color',
            'category_bar_type',
            'today_special_icon',
            'theme_preview_image',
            'search_box_icon_color',
            'read_more_link_color',
            'read_more_link_label',
            'banner_height',
            'label_color_transparency',
            'item_box_background_color',
            'item_title_color',
            'item_description_color',
        ]);

        $settings = [];

        foreach($keys as $key)
        {
            $query = ThemeSettings::select('value')->where('key',$key)->where('theme_id',$themeID)->first();
            $settings[$key] = isset($query->value) ? $query->value : '';
        }

        return $settings;
    }


    // Get Language Details
    function getLangDetails($langID)
    {
        $language = Languages::where('id',$langID)->first();
        return $language;
    }


    // Function for Genrate random Token
    function genratetoken($length = 32)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $max = strlen($string) - 1;
        $token = '';

        for ($i = 0; $i < $length; $i++)
        {
            $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }


    // Check Schedule
    function checkCategorySchedule($catID,$shop_id)
    {
        $current_date = Carbon::now();
        $today = strtolower($current_date->format('l'));
        $current_time = strtotime($current_date->format('G:i'));
        $cat_details = Category::where('id',$catID)->where('shop_id',$shop_id)->first();
        $schedule = (isset($cat_details['schedule'])) ? $cat_details['schedule'] : 0;

        if($schedule == 0)
        {
            return 1;
        }
        else
        {
            $schedule_type = (isset($cat_details['schedule_type']) && !empty($cat_details['schedule_type'])) ? $cat_details['schedule_type'] : 'time';

            if($schedule_type == 'time')
            {
                $schedule_arr = (isset($cat_details['schedule_value']) && !empty($cat_details['schedule_value'])) ? json_decode($cat_details['schedule_value'],true) : '';
                if(count($schedule_arr) > 0)
                {
                    $current_day = (isset($schedule_arr[$today])) ? $schedule_arr[$today] : '';
                    if(isset($current_day['enabled']) && $current_day['enabled'] == 1)
                    {
                        $time_schedule_arr = isset($current_day['timesSchedules']) ? $current_day['timesSchedules'] : [];

                        if(count($time_schedule_arr) > 0)
                        {
                            $count = 1;
                            $total_count = count($time_schedule_arr);
                            foreach($time_schedule_arr as $tsarr)
                            {
                                $start_time = strtotime($tsarr['startTime']);
                                $end_time = strtotime($tsarr['endTime']);

                                if($current_time > $start_time && $current_time < $end_time)
                                {
                                    return 1;
                                }
                                else
                                {
                                    if($count == $total_count)
                                    {
                                        return 0;
                                    }
                                }
                                $count ++;
                            }
                        }
                        else
                        {
                            return 0;
                        }
                    }
                    else
                    {
                        return 0;
                    }
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                $start_date =  strtotime($cat_details['sch_start_date']);
                $end_date =  strtotime($cat_details['sch_end_date']);

                if(empty($start_date) || empty($end_date))
                {
                    return 1;
                }
                else
                {
                    $curr_date = strtotime($current_date);

                    if($curr_date > $start_date && $curr_date < $end_date)
                    {
                        return 1;
                    }
                    else
                    {
                        return 0;
                    }

                }

            }
        }
    }


    // Check Delivery Schedule
    function checkDeliverySchedule($shop_id)
    {
        $current_date = Carbon::now();
        $today = strtolower($current_date->format('l'));
        $current_time = strtotime($current_date->format('G:i'));

        // Order Settings
        $sch_enable_setting = OrderSetting::where('shop_id',$shop_id)->where('key','scheduler_active')->first();
        $sch_array_setting = OrderSetting::where('shop_id',$shop_id)->where('key','schedule_array')->first();

        $schedule = (isset($sch_enable_setting['value']) && $sch_enable_setting['value'] == 1) ? 1 : 0;
        $schedule_arr = (isset($sch_array_setting['value']) && !empty($sch_array_setting['value'])) ? json_decode($sch_array_setting['value'],true) : '';

        if($schedule == 0)
        {
            return 1;
        }
        else
        {
            if(count($schedule_arr) > 0)
            {
                $current_day = (isset($schedule_arr[$today])) ? $schedule_arr[$today] : '';
                if(isset($current_day['enabled']) && $current_day['enabled'] == 1)
                {
                    $time_schedule_arr = isset($current_day['timesSchedules']) ? $current_day['timesSchedules'] : [];

                    if(count($time_schedule_arr) > 0)
                    {
                        $count = 1;
                        $total_count = count($time_schedule_arr);
                        foreach($time_schedule_arr as $tsarr)
                        {
                            $start_time = strtotime($tsarr['startTime']);
                            $end_time = strtotime($tsarr['endTime']);

                            if($current_time > $start_time && $current_time < $end_time)
                            {
                                return 1;
                            }
                            else
                            {
                                if($count == $total_count)
                                {
                                    return 0;
                                }
                            }
                            $count ++;
                        }
                    }
                    else
                    {
                        return 0;
                    }
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 0;
            }
        }

    }


    // Check Store Schedule
    function checkStoreSchedule($shop_id)
    {
        $current_date = Carbon::now();
        $today = strtolower($current_date->format('l'));
        $current_time = strtotime($current_date->format('G:i'));

        $shop_schedule = ShopSchedule::where('shop_id',$shop_id)->first();

        $schedule = (isset($shop_schedule['status']) && $shop_schedule['status'] == 1) ? 1 : 0;
        $schedule_arr = (isset($shop_schedule['value']) && !empty($shop_schedule['value'])) ? json_decode($shop_schedule['value'],true) : [];

        if($schedule == 0)
        {
            return 1;
        }
        else
        {
            if(count($schedule_arr) > 0)
            {
                $current_day = (isset($schedule_arr[$today])) ? $schedule_arr[$today] : '';
                if(isset($current_day['enabled']) && $current_day['enabled'] == 1)
                {
                    $time_schedule_arr = isset($current_day['timesSchedules']) ? $current_day['timesSchedules'] : [];

                    if(count($time_schedule_arr) > 0)
                    {
                        $count = 1;
                        $total_count = count($time_schedule_arr);
                        foreach($time_schedule_arr as $tsarr)
                        {
                            $start_time = strtotime($tsarr['startTime']);
                            $end_time = strtotime($tsarr['endTime']);

                            if($current_time > $start_time && $current_time < $end_time)
                            {
                                return 1;
                            }
                            else
                            {
                                if($count == $total_count)
                                {
                                    return 0;
                                }
                            }
                            $count ++;
                        }
                    }
                    else
                    {
                        return 0;
                    }
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 0;
            }
        }
    }


    // Function for Check Delivery Available in Customer Zone
    function checkDeliveryAvilability($latitude,$longitude)
    {
        $delivery_areas = DeliveryAreas::get();
        $inside = 0;

        if(count($delivery_areas) > 0)
        {
            foreach($delivery_areas as $delivery_area)
            {
                $coordinates = (isset($delivery_area['coordinates']) && !empty($delivery_area['coordinates'])) ? unserialize($delivery_area['coordinates']) : '';

                $vertices = $coordinates;
                $vertexCount = count($vertices);

                for ($i = 0, $j = $vertexCount - 1; $i < $vertexCount; $j = $i++)
                {
                    $xi = $vertices[$i]['lat'];
                    $yi = $vertices[$i]['lng'];
                    $xj = $vertices[$j]['lat'];
                    $yj = $vertices[$j]['lng'];

                    $intersect = (($yi > $longitude) != ($yj > $longitude)) && ($latitude < ($xj - $xi) * ($longitude - $yi) / ($yj - $yi) + $xi);

                    if ($intersect)
                    {
                        $inside = 1;
                    }
                }

            }
        }
        else
        {
            $inside = 0;
        }
        return $inside;
    }


    // Get Item Details
    function itemDetails($itemID)
    {
        $item_details = Items::with(['category'])->where('id',$itemID)->first();
        return $item_details;
    }


    // Function for get client PayPal Config
    function getPayPalConfig()
    {
        // Get Payment Settings
        $payment_settings = getPaymentSettings();

        $paypal_config = [
            'client_id' => (isset($payment_settings['paypal_public_key'])) ? $payment_settings['paypal_public_key'] : '',
            'secret' => (isset($payment_settings['paypal_private_key'])) ? $payment_settings['paypal_private_key'] : '',
            'settings' => [
                'mode' => (isset($payment_settings['paypal_mode'])) ? $payment_settings['paypal_mode'] : '',
                'http.ConnectionTimeOut' => 30,
                'log.LogEnabled' => 1,
                'log.FileName' => storage_path() . '/logs/paypal.log',
                'log.LogLevel' => 'ERROR',
            ]
        ];
        return $paypal_config;
    }

?>
