<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\{Amount, Details, Item,ItemList,Payer,Payment,PaymentExecution,RedirectUrls,Transaction};
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use App\Models\{Items,Shop,AdditionalLanguage,ItemPrice, OptionPrice, Order, OrderItems, User, UserShop};
use Exception;
use Illuminate\Support\Facades\Auth;
use Magarrent\LaravelCurrencyFormatter\Facades\Currency;

class PaypalController extends Controller
{
    private $_api_context;

    public function payWithpaypal()
    {
        $final_amount = \Cart::getTotal();
        // Admin Details
        $user_details = User::where('id',1)->where('user_type',2)->first();
        $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
        $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;

        $all_item = [];
        $checkout_type = session()->get('checkout_type');
        $discount_per = session()->get('discount_per');
        $discount_type = session()->get('discount_type');

        if(empty($checkout_type))
        {
            return redirect()->route('home')->with('error','UnAuthorized Request!');
        }

        // Admin Settings
        $shop_settings = getClientSettings();
        $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'USD';

        $paypal_config = getPayPalConfig();
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_config['client_id'],
            $paypal_config['secret'])
        );
        $this->_api_context->setConfig($paypal_config['settings']);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Get Cart Details
        $cart = \Cart::getContent();

        if(count($cart) == 0)
        {
            return redirect()->route('home');
        }

        // Add Items
        foreach($cart as $cart_data)
        {
            $item_name = $cart_data['name'];
            $item_quantity = $cart_data['quantity'];
            $item_price = $cart_data['price'];

            $item = new Item();
            $item->setName($item_name);
            $item->setCurrency($currency);
            $item->setQuantity($item_quantity);
            $item->setPrice($item_price);
            $all_item[] = $item;
        }

        // GST Amount
        if($cgst > 0 && $sgst > 0)
        {
            $gst_per =  $cgst + $sgst;

            if(count($all_item) > 0)
            {
                foreach($all_item as $key=> $a_item)
                {
                    $all_item[$key]->price = $a_item->price + ($a_item->price * $gst_per) / 100;
                }
            }

            $final_amount += ($final_amount * $gst_per) / 100;
        }


        $item_list = new ItemList();
        $item_list->setItems($all_item);

        $amount = new Amount();
        $amount->setCurrency($currency);

        $final_amount = number_format($final_amount,2);

        if($discount_per > 0)
        {
            if($discount_type == 'fixed')
            {
                $discount_amount = $discount_per;
            }
            else
            {
                $discount_amount = number_format(($final_amount * $discount_per) / 100,2);
            }
            $total = number_format($final_amount - $discount_amount,2);

            $amount->setTotal($total);
            $amount->setDetails( new Details([
                'subtotal' => $final_amount,
                'discount' => number_format($discount_amount,2),
                'currency' => $currency,
            ]));
        }
        else
        {
            $amount->setTotal($final_amount);
        }

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($item_list)->setDescription('Your transaction description')->setInvoiceNumber(uniqid());

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('paypal.payment.status'))->setCancelUrl(route('paypal.payment.status'));

        $payment = new Payment();
        $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions(array($transaction));

        try
        {
            $payment->create($this->_api_context);
        }
        catch (Exception $ex)
        {
            return redirect()->route('home')->with('error','Payment Failed!');
        }

        foreach($payment->getLinks() as $link)
        {
            if($link->getRel() == 'approval_url')
            {
                $redirect_url = $link->getHref();
                break;
            }
        }

        // add payment ID to session
        session()->put('paypal_payment_id', $payment->getId());
        session()->save();

        if(isset($redirect_url))
        {
            // redirect to paypal
            return redirect($redirect_url);
        }
    }


    public function paymentCancel()
    {
       return redirect()->route('home')->with('error','Payment Cancel!');
    }


    public function getPaymentStatus(Request $request)
    {
        $cart = \Cart::getContent();
        $discount_per = session()->get('discount_per');
        $discount_type = session()->get('discount_type');
        $checkout_type = session('checkout_type','');
        $order_details = session()->get('order_details');

        // Admin Details
        $user_details = User::where('id',1)->where('user_type',2)->first();
        $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
        $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;

        // Admin Settings
        $shop_settings = getClientSettings();
        $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'USD';

        // Order Settings
        $order_settings = getOrderSettings();

        $total_amount = $order_details['total_amount'];
        $total_amount_text = Currency::currency($currency)->format($total_amount);
        $cart_subtotal = \Cart::getTotal();
        $cart_subtotal_text = Currency::currency($currency)->format($cart_subtotal);
        $user_ip = $request->ip();
        $payment_method = $order_details['payment_method'];
        $email = $order_details['email'];
        $phone_number = $order_details['phone_number'];
        $instructions = $order_details['instructions'];
        $firstname = $order_details['firstname'];
        $lastname = $order_details['lastname'];
        $latitude = (isset($order_details['latitude'])) ? $order_details['latitude'] : '';
        $longitude = (isset($order_details['longitude'])) ? $order_details['longitude'] : '';
        $address = (isset($order_details['address'])) ? $order_details['address'] : '';
        $street_number = (isset($order_details['street_number'])) ? $order_details['street_number'] : '';
        $city = (isset($order_details['city'])) ? $order_details['city'] : '';
        $state = (isset($order_details['state'])) ? $order_details['state'] : '';
        $postcode = (isset($order_details['postcode'])) ? $order_details['postcode'] : '';
        $cart_qty = \Cart::getTotalQuantity();

        $paypal_config = getPayPalConfig();
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_config['client_id'],
            $paypal_config['secret'])
        );
        $this->_api_context->setConfig($paypal_config['settings']);

        // Get the payment ID before session clear
        $payment_id = session()->get('paypal_payment_id');

        if(empty($request->PayerID) || empty($request->token))
        {
            return redirect()->route('home')->with('error', 'Payment failed!');
        }

        $payment = Payment::get($payment_id, $this->_api_context);

        // PaymentExecution object includes information necessary
        // to execute a PayPal account payment.
        // The payer_id is added to the request query parameters
        // when the user is redirected from paypal back to your site
        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);

        //Execute the payment
        try
        {
            $result = $payment->execute($execution, $this->_api_context);
        }
        catch (\Throwable $th)
        {
            return redirect()->route('home')->with('error','Payment Failed!');
        }

        if($result->getState() == 'approved') // payment made
        {
            // New Order
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->ip_address = $user_ip;
            $order->currency = $currency;
            $order->checkout_type = $checkout_type;
            $order->payment_method = $payment_method;
            $order->order_status = 'pending';
            $order->is_new = 1;
            $order->estimated_time = (isset($order_settings['order_arrival_minutes']) && !empty($order_settings['order_arrival_minutes'])) ? $order_settings['order_arrival_minutes'] : '30';
            $order->firstname = $firstname;
            $order->lastname = $lastname;
            $order->email = $email;
            $order->phone = $phone_number;
            $order->instructions = $instructions;

            if($checkout_type == 'takeaway'){
                $order->pickup_location = $order_details['pickup_location'];
            }

            // If Checkout Type is Delivery Then Insert More Details
            if($checkout_type == 'delivery')
            {
                $order->address = $address;
                $order->latitude = $latitude;
                $order->longitude = $longitude;
                $order->city = $city;
                $order->state = $state;
                $order->postcode = $postcode;
                $order->street_number = $street_number;
            }

            $order->save();

            // Insert Order Items
            if($order->id)
            {
                foreach($cart as $cart_data)
                {
                    $item_id = $cart_data['id'];
                    $item_name = $cart_data['name'];
                    $item_quantity = $cart_data['quantity'];
                    $item_price = $cart_data['price'];
                    $item_price_text = Currency::currency($currency)->format($item_price);
                    $item_subtotal = $item_price * $item_quantity;
                    $item_subtotal_text = Currency::currency($currency)->format($item_subtotal);
                    $options = (isset($cart_data['attributes']) && count($cart_data['attributes']) > 0) ? serialize($cart_data['attributes']->toArray()) : '';

                    // Order Items
                    $order_items = new OrderItems();
                    $order_items->order_id = $order->id;
                    $order_items->item_id = $item_id;
                    $order_items->item_name = $item_name;
                    $order_items->item_price = $item_price;
                    $order_items->item_qty = $item_quantity;
                    $order_items->sub_total = $item_subtotal;
                    $order_items->sub_total_text = $item_subtotal_text;
                    $order_items->options = $options;
                    $order_items->save();
                }

                $update_order = Order::find($order->id);
                $update_order->order_subtotal = $cart_subtotal;

                if($discount_per > 0)
                {
                    if($discount_type == 'fixed')
                    {
                        $discount_amount = $discount_per;
                    }
                    else
                    {
                        $discount_amount = ($cart_subtotal * $discount_per) / 100;
                    }

                    $update_order->discount_per = $discount_per;
                    $update_order->discount_type = $discount_type;
                    $update_order->discount_value = $discount_amount;

                    $cart_subtotal = $cart_subtotal - $discount_amount;
                }

                // CGST & SGST
                if($cgst > 0 && $sgst > 0)
                {
                    $gst_per =  $cgst + $sgst;
                    $gst_amount = ( $cart_subtotal * $gst_per) / 100;
                    $update_order->cgst = $cgst;
                    $update_order->sgst = $sgst;
                    $update_order->gst_amount = $gst_amount;
                    $cart_subtotal += $gst_amount;
                }

                $update_order->order_total = $total_amount;
                $update_order->order_total_text = $total_amount_text;
                $update_order->total_qty = $cart_qty;
                $update_order->update();
            }

            \Cart::clear();
            session()->forget('order_details');
            session()->forget('paypal_payment_id');
            session()->forget('discount_per');
            session()->forget('discount_type');
            session()->forget('checkout_type');
            session()->forget('cust_lat');
            session()->forget('cust_long');
            session()->forget('cust_address');
            session()->save();

            // return redirect()->route('cart.checkout.success',encrypt($order->id));
            return redirect()->route('customer.orders.details',[encrypt($order->id)])->with('success','Your Order has been Placed SuccessFully....');
        }

        return redirect()->route('paypal.payment.cancel');
    }
}
