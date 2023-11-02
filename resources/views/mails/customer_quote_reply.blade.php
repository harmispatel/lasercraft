@php
    $user_details = (isset($details['user_details'])) ? $details['user_details'] : '';
    $cust_name =  $details['quote_details']['firstname']." ".$details['quote_details']['lastname'];
    $message = $details['message'];
    $message = str_replace('[customer_name]', $cust_name, $message);
    $products = $details['products'];
    $currancy = $details['currency'];
    $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;
    $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;

    $total = 0;

    // Get Product Total
    if(isset($products) && count($products) > 0)
    {
        $items = (isset($products['item'])) ? $products['item'] : '';
        $prices = (isset($products['price'])) ? $products['price'] : '';
        $quantities = (isset($products['qty'])) ? $products['qty'] : '';
        $discounts = (isset($products['discount'])) ? $products['discount'] : '';

        if(count($items) > 0)
        {
            foreach ($items as $key => $item)
            {
                $price = (isset($prices[$key])) ? $prices[$key] : 0;
                $discount = (isset($discounts[$key])) ? $discounts[$key] : 0;
                $quantity = (isset($quantities[$key])) ? $quantities[$key] : 0;
                $discount_val = $price * $quantity;
                $discount_val =($discount_val ) * $discount / 100;
                $item_total = ($price * $quantity);
                $item_total = $item_total - $discount_val;
                $total += $item_total;
            }
        }

        if($cgst > 0 && $sgst > 0)
        {
            $cgst_amount = ($total * $cgst) / 100;
            $sgst_amount = ($total * $sgst) / 100;
            $total = $total + $sgst_amount + $cgst_amount;
        }
    }

    if($details['doc_name'] == 'Invoice'){
        $invoice_id =  $details['invoice_id'];
        $message = str_replace('[invoice_number]', $invoice_id, $message);
        $message = str_replace('[invoice_date]', date('M d, Y'), $message);
        $message = str_replace('[amount_due]', Currency::currency($currancy)->format($total), $message);
    }

@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mahantam Laser Crafts</title>
</head>

<body width="100%" style="margin: 0; padding: 30px 0 !important; mso-line-height-rule: exactly; background-color: #fff; font-family: sans-serif; color: #676767; -webkit-print-color-adjust: exact;">
    <div style="width: 100%; background-color: #fff;">
        <div style="max-width: 500px; margin: 0 auto; background: #fff;">
            <p>{!! $message !!}</p>
        </div>

        {{-- <div style="max-width: 500px; margin: 0 auto; background: #fff;">
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                <tr>
                    <td valign="top" class="bg_white" style="background-color: #f5f5f5;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="text-align: center; padding: 20px 15px 0;">
                                    <h2 style="margin:0;color: #000;">MAHANTAM LASER CRAFTS</h2>
                                    @if(isset($details['user']['mobile']) && !empty($details['user']['mobile']))
                                        <h3 style="margin:0;color: #000;">{{ $details['user']['mobile'] }}</h3>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding: 0px 15px;">
                                    <p style="margin-bottom: 0;">{{ (isset($details['user']['address'])) ? $details['user']['address'] : '' }}</p>
                                </td>
                            </tr>
                            @if(isset($details['user']['gst_number']) && !empty($details['user']['gst_number']))
                                <tr>
                                    <td style="text-align: center; padding: 0px 15px;">
                                        <p><strong>GST No. : </strong> {{ $details['user']['gst_number'] }}</p>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="bg_white email-section" style="padding: 0 20px 0px; background-color: #f5f5f5;border-top:3px solid #fff;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="background: #f5f5f5;">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0 auto;">
                                        <tr>
                                            <td width="50%" valign="center" style="padding: 10px 0px 0;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="center" style="padding: 5px 0px; font-size: 15px; color: #666666;"><strong>Quotation ID. : </strong> {{ $details['quote_details']['id'] }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" valign="center" style="padding: 0px 0px;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="center" style="padding: 5px 0px; font-size: 15px; color: #666666;"><strong>Customer Name : </strong> {{ $details['quote_details']['firstname'] }} {{ $details['quote_details']['lastname'] }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" valign="center" style="padding: 0px 0px;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="center" style="padding: 5px 0px; font-size: 15px; color: #666666;"><strong>Order Date : </strong> {{ date('d-m-Y h:i:s',strtotime($details['quote_details']['created_at'])) }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 20px 20px; background-color: #f5f5f5;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="text-align: left;padding: 10px 0; border-bottom:2px solid #fff;">Description</th>
                                    <th style="text-align: end;padding: 10px 0; border-bottom:2px solid #fff;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @php
                                        $desc = $details['quote_details']['message'];
                                        $desc = substr("$desc",0,50);
                                    @endphp
                                    <td style="padding: 10px 0;">{{ $desc }}....
                                        <br>
                                        @if(isset($details['quote_details']['document']))
                                            <span style="color:rgb(35, 35, 243)"> Doc : {{ $details['quote_details']['document'] }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 10px 0;text-align: end;">{{ Currency::currency($details['currency'])->format($details['price']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 20px 20px; background-color: #f5f5f5;border-top:3px solid #fff;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody>
                                @php
                                    $cgst = (isset($details['user']['cgst'])) ? $details['user']['cgst'] : 0;
                                    $sgst = (isset($details['user']['sgst'])) ? $details['user']['sgst'] : 0;
                                    $total_amount = $details['price'];
                                @endphp

                                @if($cgst > 0 && $sgst > 0)
                                    <tr>
                                        <td colspan="2" style="padding: 10px 0;text-align: left;"><strong>CGST ({{ $cgst }}%)</strong></td>
                                        <td colspan="2" style="padding: 10px 0;text-align: end;"><strong>+ {{ Currency::currency($details['currency'])->format(($total_amount * $cgst) / 100) }}</strong></td>
                                        @php
                                            $cgst_amount = ($total_amount * $cgst) / 100;
                                        @endphp
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 10px 0; border-top:1px solid #fff;text-align: left;"><strong>SGST ({{ $sgst }}%)</strong></td>
                                        <td colspan="2" style="padding: 10px 0; border-top:1px solid #fff;text-align: end;"><strong>+ {{ Currency::currency($details['currency'])->format(($total_amount * $sgst) / 100) }}</strong></td>
                                        @php
                                            $sgst_amount = ($total_amount * $sgst) / 100;
                                            $total_amount = $total_amount + $sgst_amount + $cgst_amount;
                                        @endphp
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="2" style="padding: 10px 0; border-top:1px solid #fff;text-align: left;"><strong>Total Amount</strong></td>
                                    <td colspan="2" style="padding: 10px 0; border-top:1px solid #fff;text-align: end;"><strong>{{ Currency::currency($details['currency'])->format($total_amount) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div> --}}

    </div>
</body>

</html>
