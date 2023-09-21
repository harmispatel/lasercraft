<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Items;
use App\Models\Languages;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Magarrent\LaravelCurrencyFormatter\Facades\Currency;

class CartController extends Controller
{

    // Get User Cart List
    function cartList()
    {
        // Order Settings
        $order_settings = getOrderSettings();
        $discount_per = (isset($order_settings['discount_percentage']) && ($order_settings['discount_percentage'] > 0)) ? $order_settings['discount_percentage'] : 0;
        $discount_type = (isset($order_settings['discount_type'])) ? $order_settings['discount_type'] : 'percentage';
        session()->put('discount_per',$discount_per);
        session()->put('discount_type',$discount_type);
        session()->save();

        // Child Categories
        $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();

        $cart_items = \Cart::getContent();

        return view('frontend.view_cart',compact(['child_categories','cart_items']));
    }


    // Add to Cart
    public function addToCart(Request $request)
    {

        // Language Settings
        $language_settings = clientLanguageSettings();
        $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

        // Language Details
        $language_detail = Languages::where('id',$primary_lang_id)->first();
        $lang_code = isset($language_detail->code) ? $language_detail->code : '';

        $name_key = $lang_code."_name";

        $item_id = $request->item_id;

        $item_details = Items::find($item_id);
        $item_name = (isset($item_details[$name_key])) ? $item_details[$name_key] : "";

        $item_price = $request->item_price;
        $quantity = $request->quantity;
        $personalised_message = $request->personalised_message;
        $attributes = (isset($request->attr_option) && count($request->attr_option) > 0) ? $request->attr_option : [];

        \Cart::add([
            'id' => $item_id,
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => $quantity,
            'attributes' => $attributes,
            'per_message' => $personalised_message,
        ]);

        return redirect()->route('cart.list')->with('success','Product is Added to Cart Successfully !');
    }


    // Remove Cart
    public function removeCart($item_id)
    {
        \Cart::remove($item_id);

        return redirect()->route('cart.list')->with('success', 'Remove Item from Cart Successfully !');
    }


    // Cart Checkout
    public function cartCheckout()
    {
        $cart = \Cart::getContent();

        if(count($cart) == 0)
        {
            return redirect()->route('home');
        }

        // Child Categories
        $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();

        return view('frontend.view_checkout',compact(['child_categories']));
    }


    // Cart Update
    public function updateCart(Request $request)
    {
        try {

            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                ]
            );

            return response()->json([
                'success' => 1,
                'message' => 'Cart has been Updated Successfully !',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error !',
            ]);
        }
    }


    // Cart Checkout Process
    public function cartCheckoutPost(Request $request)
    {
        $checkout_type = session('checkout_type','');

        $rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'payment_method' => 'required',
        ];

        if($checkout_type == 'delivery')
        {
            $rules += [
                'address' => 'required',
                'street_number' => 'required',
                'floor' => 'required',
                'door_bell' => 'required',
            ];
        }

        $request->validate($rules);

        try
        {
            // Admin Details
            $user_details = User::where('id',1)->where('user_type',2)->first();
            $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
            $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;

            // Admin Settings
            $shop_settings = getClientSettings();
            $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'USD';

            // Order Settings
            $order_settings = getOrderSettings();

            $total_amount = $request->total_amount;
            $total_amount_text = Currency::currency($currency)->format($total_amount);
            $cart_subtotal = \Cart::getTotal();
            $cart_subtotal_text = Currency::currency($currency)->format($cart_subtotal);
            $user_ip = $request->ip();
            $payment_method = $request->payment_method;
            $email = $request->email;
            $phone_number = $request->phone_number;
            $instructions = $request->instructions;
            $firstname = $request->firstname;
            $lastname = $request->lastname;
            $latitude = (isset($request->latitude)) ? $request->latitude : '';
            $longitude = (isset($request->longitude)) ? $request->longitude : '';
            $address = (isset($request->address)) ? $request->address : '';
            $street_number = (isset($request->street_number)) ? $request->street_number : '';
            $floor = (isset($request->floor)) ? $request->floor : '';
            $door_bell = (isset($request->door_bell)) ? $request->door_bell : '';
            $discount_per = session()->get('discount_per');
            $discount_type = session()->get('discount_type');
            $cart = \Cart::getContent();
            $cart_qty = \Cart::getTotalQuantity();

            if($checkout_type == 'delivery')
            {
                $delivey_avaialbility = checkDeliveryAvilability($latitude,$longitude);

                if($delivey_avaialbility == 0)
                {
                    $validator = Validator::make([], []);
                    $validator->getMessageBag()->add('address', 'Sorry your address is out of our delivery range.');
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }

            if(count($cart) == 0)
            {
                return redirect()->route('home');
            }

            if($payment_method == 'cash'){

                // New Order
                $order = new Order();
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

                // If Checkout Type is Delivery Then Insert More Details
                if($checkout_type == 'delivery')
                {
                    $order->address = $address;
                    $order->latitude = $latitude;
                    $order->longitude = $longitude;
                    $order->floor = $floor;
                    $order->door_bell = $door_bell;
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
            }
            elseif($payment_method == 'paypal'){
                session()->put('order_details',$request->all());
                session()->save();
                return redirect()->route('paypal.payment');
            }

            \Cart::clear();
            session()->forget('discount_per');
            session()->forget('discount_type');
            session()->forget('checkout_type');
            session()->forget('cust_lat');
            session()->forget('cust_long');
            session()->forget('cust_address');

            return redirect()->route('cart.checkout.success',[encrypt($order->id)]);

        }
        catch (\Throwable $th)
        {
            return redirect()->back()->with('error','Internal Server Error!');
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


    // Set Checkout Type
    public function setCartCheckoutType(Request $request)
    {
        $checkout_type = $request->check_type;

        try
        {
            session()->put('checkout_type',$checkout_type);
            session()->save();

            return response()->json([
                'success' => 1,
                "message" => "Redirecting to Checkout SuccessFully...",
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                "message" => "Internal server error!",
            ]);
        }
    }


    // Function for redirect Checkout Success
    public function cartCheckoutSuccess($orderID)
    {
        try
        {
            // Child Categories
            $data['child_categories'] = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();

            $order_id = decrypt($orderID);

            $data['order_details'] = Order::where('id',$order_id)->first();

            if(empty($data['order_details'])){
                return redirect()->route('home')->with('error','Something Went Wrong!');
            }

            return view('frontend.checkout_success',$data);
        }
        catch (\Throwable $th)
        {
           return redirect()->route('home')->with('error','Internal Server Error!');
        }
    }


    // Function for Check Order Status
    public function checkOrderStatus(Request $request)
    {
        $order_id = $request->order_id;
        $order = Order::where('id',$order_id)->first();
        $order_status = (isset($order['order_status'])) ? $order['order_status'] : '';
        return response()->json([
            'success' => 1,
            'status' => $order_status,
        ]);
    }
}
