@php

    // Language Settings
    $language_settings = clientLanguageSettings();
    $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

    // Language Details
    $language_detail = App\Models\Languages::where('id',$primary_lang_id)->first();
    $lang_code = isset($language_detail->code) ? $language_detail->code : '';

    $description_key = $lang_code."_description";
    $image_key = $lang_code."_image";
    $name_key = $lang_code."_name";
    $title_key = $lang_code."_title";

    $client_settings = getClientSettings();
    $top_logo = (isset($client_settings['shop_view_header_logo'])) ? $client_settings['shop_view_header_logo'] : '';
    $top_logo = (!empty($top_logo) && file_exists('public/client_uploads/top_logos/'.$top_logo)) ? asset('public/client_uploads/top_logos/'.$top_logo) : asset('public/client_images/not-found/no_image_1.jpg');
    $top_logo = '<img src='.$top_logo.' width="80">';

    $currency = (isset($datails['currency'])) ? $datails['currency'] : 'AUD';

    $order_details = (isset($details['order_details'])) ? $details['order_details'] : '';
    $first_name = (isset($details['order_details']['firstname'])) ? $details['order_details']['firstname'] : '';
    $last_name = (isset($details['order_details']['lastname'])) ? $details['order_details']['lastname'] : '';
    $order_id = (isset($details['order_details']['id'])) ? $details['order_details']['id'] : '';
    $shipping_method = (isset($details['order_details']['checkout_type'])) ? $details['order_details']['checkout_type'] : '';
    $payment_method = (isset($details['order_details']['payment_method'])) ? $details['order_details']['payment_method'] : '';
    $phone_number = (isset($details['order_details']['phone'])) ? $details['order_details']['phone'] : '';
    $comments = (isset($details['order_details']['instructions'])) ? $details['order_details']['instructions'] : '';
    $order_items = (isset($details['order_details']['order_items']) && count($details['order_details']['order_items']) > 0) ? $details['order_details']['order_items'] : [];

    $mail_format = $details['mail_format'];
    $mail_format = str_replace('{shop_logo}',$top_logo,$mail_format);
    $mail_format = str_replace('{firstname}',$first_name,$mail_format);
    $mail_format = str_replace('{lastname}',$last_name,$mail_format);
    $mail_format = str_replace('{order_id}',$order_id,$mail_format);
    $mail_format = str_replace('{shipping_method}',$shipping_method,$mail_format);
    $mail_format = str_replace('{payment_method}',$payment_method,$mail_format);
    $mail_format = str_replace('{phone_number}',$phone_number,$mail_format);
    $mail_format = str_replace('{comments}',$comments,$mail_format);

    // Order Items
    $order_html  = "";
    $order_html .= '<div>';
        $order_html .= '<table style="width:100%; border:1px solid gray;border-collapse: collapse;">';
            $order_html .= '<thead style="background:lightgray; color:white">';
                $order_html .= '<tr style="text-transform: uppercase!important; font-weight: 700!important;">';
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
                        $item_images = App\Models\ItemImages::where('item_id',$order_item['item_id'])->get();

                        $item_image = (count($item_images) > 0  && isset($item_images[0]['image']) && !empty($item_images[0]['image']) && file_exists('public/client_uploads/items/'.$item_images[0]['image'])) ? asset('public/client_uploads/items/'.$item_images[0]['image']) : asset('public/client_images/not-found/no_image_1.jpg');
                        $attributes = (isset($order_item['options']) && !empty($order_item['options'])) ? unserialize($order_item['options']) : [];

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

                                        if(count($attributes) > 0)
                                        {
                                            foreach ($attributes as $attribute)
                                            {
                                                $option_price = App\Models\OptionPrice::with(['option'])->where('id',$attribute)->first();
                                                $option_name = (isset($option_price['option'][$title_key])) ? $option_price['option'][$title_key] : '';
                                                $price_name = (isset($option_price[$name_key])) ? $option_price[$name_key] : '';

                                                $order_html .= '<div style="color: #a19e9e;display: block;"><strong>'.$option_name.' : </strong>'.$price_name.'</div>';
                                            }
                                        }

                                        $order_html .= '<div style="color: #a19e9e;display: block;"><strong>Personalised Message : </strong>'.$order_item['personalised_message'].'</div>';

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
    $mail_format = str_replace('{items}',$order_html,$mail_format);

    // Order Total
    $order_total_html = "";
    $order_total_html .= '<div>';
        $order_total_html .= '<table style="width:65%; border:1px solid gray;border-collapse: collapse;">';
            $order_total_html .= '<tbody style="font-weight: 700!important;">';
                $order_total_html .= '<tr>';
                    $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">Sub Total : </td>';
                    $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">'.Currency::currency($currency)->format($order_details->order_subtotal).'</td>';
                $order_total_html .= '</tr>';

                if($order_details->discount_per > 0)
                {
                    $order_total_html .= '<tr>';
                        $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">Discount : </td>';
                        if($order_details->discount_per == 'fixed')
                        {
                            $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">- '.Currency::currency($currency)->format($order_details->discount_per).'</td>';
                        }
                        else
                        {
                            $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">- '.$order_details->discount_per.'%</td>';
                        }
                    $order_total_html .= '</tr>';
                }

                if($order_details->cgst > 0 && $order_details->sgst > 0)
                {
                    $gst_amt = $order_details->cgst + $order_details->sgst;
                    $gst_amt = $order_details->gst_amount / $gst_amt;

                    $order_total_html .= '<tr>';
                        $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">'.__('CGST.').' ('.$order_details->cgst.'%)</td>';
                        $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">+ '.Currency::currency($currency)->format($order_details->cgst * $gst_amt).'</td>';
                    $order_total_html .= '</tr>';
                    $order_total_html .= '<tr>';
                        $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">'.__('SGST.').' ('.$order_details->sgst.'%)</td>';
                        $order_total_html .= '<td style="padding:10px; border-bottom:1px solid gray">+ '.Currency::currency($currency)->format($order_details->sgst * $gst_amt).'</td>';
                    $order_total_html .= '</tr>';
                }

                $order_total_html .= '<tr>';
                    $order_total_html .= '<td style="padding:10px;">Total : </td>';
                    $order_total_html .= '<td style="padding:10px;">';
                        $order_total_html .= Currency::currency($currency)->format($order_details->order_total);
                    $order_total_html .= '</td>';
                $order_total_html .= '</tr>';

            $order_total_html .= '</tbody>';
        $order_total_html .= '</table>';
    $order_total_html .= '</div>';
    $mail_format = str_replace('{total}',$order_total_html,$mail_format);

@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Notify</title>
</head>
<body>
    <div>
        {!! $mail_format !!}
    </div>
</body>
</html>
