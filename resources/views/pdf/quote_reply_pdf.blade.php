@php
    $client_settings = getClientSettings();
    $top_logo = (isset($client_settings['shop_view_header_logo'])) ? $client_settings['shop_view_header_logo'] : '';
    $top_logo = (!empty($top_logo) && file_exists('public/client_uploads/top_logos/'.$top_logo)) ? base64_encode(file_get_contents(public_path('client_uploads/top_logos/'.$top_logo))) : base64_encode(file_get_contents(public_path('client_images/not-found/no_image_1.jpg')));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quotation PDF</title>
    <link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/font-family.css')}}">
</head>
<body style="width: 100%; margin:0;font-family: 'Outfit';">
    <center style="width: 100%;">
        <div class="invoice_main" style="max-width: 600px; margin: 0 auto;padding:30px; background: #fff;">
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                <tbody>
                    <tr>
                        <td style="background-color: #f3f4f9;padding:20px 20px 0;">
                            <div class="inv_logo" style="margin-bottom: 20px;">
                                <img src="data:image/png;base64, {!! $top_logo !!}" width="100"/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f3f4f9;padding:0 20px 20px;">
                            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                                <tr>
                                    <td style="vertical-align: baseline;">
                                        <label>From</label>
                                        <h3 style="font-size: 20px;margin:0;">{{ isset($client_settings['business_name']) ? $client_settings['business_name'] : 'Mahantam Laser Crafts' }}</h3>
                                        <address class="text-muted">{{ isset($user_details['address']) ? $user_details['address'] : '{shop_address}' }}</address>
                                        <p style="font-size: 16px;margin:0;"><strong>Email :</strong> {{ isset($user_details['email']) ? $user_details['email'] : '' }}</p>
                                        <p style="font-size: 16px;margin:0;"><strong>Phone :</strong> {{ isset($user_details['mobile']) ? $user_details['mobile'] : '' }}</p>
                                    </td>
                                    <td>
                                        <label>To</label>
                                        <h3 style="font-size: 20px;margin:0;">{{ isset($quote_details['company_name']) ? $quote_details['company_name'] : '' }}</h3>
                                        <p style="font-size: 16px;margin:0;"><strong>Customer :</strong> {{ isset($quote_details['firstname']) ? $quote_details['firstname'] : '' }} {{ isset($quote_details['lastname']) ? $quote_details['lastname'] : '' }} </p>
                                        <p style="font-size: 16px;margin:0;"><strong>Email :</strong> {{ isset($quote_details['email']) ? $quote_details['email'] : '' }}</p>
                                        <p style="font-size: 16px;margin:0;"><strong>Phone :</strong> {{ isset($quote_details['phone']) ? $quote_details['phone'] : '' }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                                <tbody>
                                    <tr>
                                        <td align="left">
                                            <p>Date of issue : {{ date('M d, Y') }}</p>
                                        </td>
                                        <td align="right">
                                            <h3 style="font-size: 20px;margin:0;">INVOICE : #{{ isset($latestReplyId) ? $latestReplyId : '' }}</h3>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        @php
                            $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;
                            $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
                        @endphp
                        <td>
                            <table role="presentation" class="table" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;border-bottom:1px solid #000;">
                                <thead>
                                    <tr>
                                        <th align="left" style="width: 80%;padding: 10px 5px;border-bottom: 1px solid #000;">Item Name</th>
                                        <th align="right" style="width: 20%;padding: 10px 5px;border-bottom: 1px solid #000;">Item Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($products) && count($products) > 0)
                                        @php
                                            $items = (isset($products['item'])) ? $products['item'] : '';
                                            $prices = (isset($products['price'])) ? $products['price'] : '';
                                            $total = 0;
                                        @endphp

                                        @if(count($items) > 0)
                                            @foreach ($items as $key => $item)
                                                @php
                                                    $price = (isset($prices[$key])) ? $prices[$key] : 0;
                                                    $total += $price;
                                                @endphp
                                                <tr>
                                                    <td align="left" style="padding: 2px 5px 2px">{{ $item }}</td>
                                                    <td align="right" style="padding: 2px 5px 2px">{{ Currency::currency($currency)->format($price) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endif
                                    <tr>
                                        <td align="left" style="border-top: 1px solid #000;padding: 10px 5px 0"><strong>SUBTOTAL</strong></td>
                                        <td align="right" style="border-top: 1px solid #000;padding: 10px 5px 0">{{ Currency::currency($currency)->format($total) }}</td>
                                    </tr>

                                    @if($cgst > 0 && $sgst > 0)
                                        <tr>
                                            <td align="left" style="padding: 5px 5px 0"><strong>CGST ({{ $cgst }}%)</strong> </td>
                                            <td align="right" style="padding: 5px 5px 0">+ {{ Currency::currency($currency)->format(($total * $cgst) / 100) }}</td>
                                            @php
                                            $cgst_amount = ($total * $cgst) / 100;
                                            @endphp
                                        </tr>
                                        <tr>
                                            <td align="left" style="padding: 5px 5px 0"><strong>SGST ({{ $sgst }}%)</strong></td>
                                            <td align="right" style="padding: 5px 5px 0">+ {{ Currency::currency($currency)->format(($total * $sgst) / 100) }}</td>
                                            @php
                                            $sgst_amount = ($total * $sgst) / 100;
                                            $total = $total + $sgst_amount + $cgst_amount;
                                            @endphp
                                        </tr>
                                    @endif
                                    <tr>
                                        <td align="left" style="padding: 5px 5px 10px"><strong>TOTAL AMOUNT</strong></td>
                                        <td align="right" style="padding: 5px 5px 0">{{ Currency::currency($currency)->format($total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            <p style="font-size: 16px;margin:15px 0 0;">Thank You for Being Our Customer</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </center>

</body>
</html>
