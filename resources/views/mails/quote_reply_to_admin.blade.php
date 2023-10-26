@php
    $user_details = $details['user_details'];
    $currancy = $details['currency'];
    $products = $details['products'];
    $fname = $details['quote_details']['firstname'];
    $full_name = $details['quote_details']['firstname']." ".$details['quote_details']['lastname'];
    $file_path = asset('public/admin_uploads/quote_replies_docs/'.$details['file_name']);
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

@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mahantam Laser Crafts</title>
</head>

<body width="100%"
    style="margin: 0; padding: 30px 0 !important; mso-line-height-rule: exactly; background-color: #fff; font-family: sans-serif; color: #676767; -webkit-print-color-adjust: exact;">
    <div style="width: 100%; background-color: #fff;">
        <div style="max-width: 500px; margin: 0 auto; background: #fff;">
            <p>
                Dear<strong> Mahantam Laser Crafts</strong>,
            </p>
            <p>
                We are writing to confirm that the {{ $details['doc_name'] }} has been successfully sent.
            </p>
            <p>
                <strong>{{ $details['doc_name'] }} Details :</strong>
            </p>
            <p>
                Recipient: {{ $full_name }}
                <br>
                {{ $details['doc_name'] }} Amount: {{ Currency::currency($currancy)->format($total) }}
                </p>
            <p>
                The {{ $details['doc_name'] }} has been dispatched and should be in {{ $fname }}'s possession for prompt processing.
            </p>
            <p>
                You can review the {{ $details['doc_name'] }} by clicking on the following link: <a target="_blank"
                    href="{{ $file_path }}">{{ $details['file_name'] }}</a>
                <br>
                &nbsp;
            </p>
            <p>
                <strong>Mahantam&nbsp;Laser&nbsp;Crafts</strong>
            </p>
        </div>
    </div>
</body>

</html>
