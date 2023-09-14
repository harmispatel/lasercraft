<?php

namespace App\Http\Controllers;

use App\Models\CustomerQuote;
use App\Models\CustomerQuoteReply;
use App\Models\User;
use Illuminate\Http\Request;
use Magarrent\LaravelCurrencyFormatter\Facades\Currency;

class CustomerQuoteController extends Controller
{

    // Function for Get All Customer Quotes
    function index()
    {
        $data['customer_quotes'] = CustomerQuote::orderBy('created_at','DESC')->get();
        return view('client.customer_quotes.customer_quotes',$data);
    }

    // Function for Get Customer Quote Details
    function quoteDetails(Request $request)
    {
        try {

            $html = '';
            $quote_id = $request->quote_id;
            $quote_details = CustomerQuote::where('id',$quote_id)->first();
            $full_name = $quote_details['firstname'] . " ". $quote_details['lastname'];

            $html .= '<div class="container">';
                $html .= '<div class="row">';
                    $html .= '<div class="col-md-12">';
                        $html .= '<div class="card mb-4">';
                            $html .= '<div class="card-body">';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-sm-3">';
                                        $html .= '<p class="mb-0">Full Name</p>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-sm-9">';
                                        $html .= '<p class="text-muted mb-0">'.$full_name.'</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<hr>';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-sm-3">';
                                        $html .= '<p class="mb-0">Email</p>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-sm-9">';
                                        $html .= '<p class="text-muted mb-0">'.$quote_details['email'].'</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<hr>';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-sm-3">';
                                        $html .= '<p class="mb-0">Phone</p>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-sm-9">';
                                        $html .= '<p class="text-muted mb-0">'.$quote_details['phone'].'</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<hr>';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-sm-3">';
                                        $html .= '<p class="mb-0">Company</p>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-sm-9">';
                                        $html .= '<p class="text-muted mb-0">'.$quote_details['company_name'].'</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<hr>';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-sm-3">';
                                        $html .= '<p class="mb-0">Document</p>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-sm-9">';
                                        $html .= '<p class="text-muted mb-0">';
                                            if(!empty($quote_details['document']) && file_exists('public/client_uploads/customer_docs/'.$quote_details['document']))
                                            {
                                                $html .= '<a href="'. asset('public/client_uploads/customer_docs/'.$quote_details['document']) .'">'.$quote_details['document'].'</a>';
                                            }
                                            else
                                            {
                                                $html .= 'Document Not Found!';
                                            }
                                        $html .='</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<hr>';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-sm-3">';
                                        $html .= '<p class="mb-0">Message</p>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-sm-9">';
                                        $html .= '<p class="text-muted mb-0">'.$quote_details['message'].'</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
                $html .= '<hr>';
                $html .= '<div class="row">';
                    $html .= '<div class="col-md-12 text-center">';
                        $html .= '<h3>Quotation Reply</h3>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-12">';
                        $html .= '<div class="card mb-4">';
                            $html .= '<div class="card-body">';
                                $html .= '<form id="quoteReplyForm" enctype="multipart/form-data">';
                                    $html .= csrf_field();
                                    $html .= '<input type="hidden" name="quote_id" id="quote_id" value="'.$quote_id.'">';
                                    $html .= '<div class="form-group mb-3">';
                                        $html .= '<label for="price" class="form-label">Price</label>';
                                        $html .= '<input type="number" name="price" id="price" class="form-control">';
                                    $html .= '</div>';
                                    $html .= '<div class="form-group mb-3">';
                                        $html .= '<label for="message" class="form-label">Message</label>';
                                        $html .= '<textarea rows="5" name="message" id="message" class="form-control" placeholder="Write Your Message here."></textarea>';
                                    $html .= '</div>';
                                    $html .= '<div class="form-group mb-3">';
                                        $html .= '<a id="btn-quote-reply" onclick="quoteReply()" class="btn btn-success"><i class="bi bi-send"></i> SEND</a>';
                                        $html .= '<button class="btn btn-success" type="button" disabled style="display:none;" id="load-btn-quote-reply"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please Wait...</button>';
                                    $html .= '</div>';
                                $html .= '</form>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';

            return response()->json([
                'success' => 1,
                'message' => 'Quote Details Fetched SuccessFully....',
                'data' => $html,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }
    }

    // Function for Quote Reply
    function quoteReply(Request $request)
    {
        $request->validate([
            'price' => 'required',
            'message' => 'required',
        ]);

        try {

            $quote_id = $request->quote_id;
            $price = $request->price;
            $message = $request->message;
            $subject = "Customer Quotation";

            $shop_settings = getClientSettings();
            $currency = (isset($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'USD';

            // Quote Details
            $quote_details = CustomerQuote::find($quote_id);
            $to_email = (isset($quote_details['email'])) ? $quote_details['email'] : '';

            $user_details = User::where('id',1)->where('user_type',2)->first();
            $contact_emails = (isset($user_details['contact_emails']) && !empty($user_details['contact_emails'])) ? unserialize($user_details['contact_emails']) : [];
            $from_email = (count($contact_emails) > 0 && isset($contact_emails[0])) ? $contact_emails[0] : '';

            if(!empty($from_email) && !empty($to_email))
            {
                $html = '';
                $html .= '<h2 style="text-align:center;">';
                    $html .= '<strong>Mahantam Laser Crafts</strong>';
                $html .= '</h2>';
                $html .= '<p>';
                    $html .= $message;
                $html .= '</p>';
                $html .= '<p>';
                    $html .= 'Your Quotation Final Price is : '. Currency::currency($currency)->format($price);
                $html .= '</p>';
                $html .= '<p style="text-align:center;">© [year] Copyright all Rights reserved by <a href="https://mahantamlasercrafts.com.au/" target="_blank"><strong>Mahantam Laser Crafts</strong></a></p>';

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                // More headers
                $headers .= 'From: <'.$from_email.'>' . "\r\n";

                mail($to_email,$subject,$html,$headers);
            }

            // Insert Customer Quote Reply
            $customer_quote_reply = new CustomerQuoteReply();
            $customer_quote_reply->quote_id = $quote_id;
            $customer_quote_reply->price = $price;
            $customer_quote_reply->message = $message;
            $customer_quote_reply->save();

            return response()->json([
                'success' => 1,
                'message' => 'Message has been Sent SuccessFully..',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }
    }
}
