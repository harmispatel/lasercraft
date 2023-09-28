<?php

namespace App\Http\Controllers;

use App\Models\CustomerQuote;
use App\Models\CustomerQuoteReply;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $quote_details = CustomerQuote::with(['quotes_replys'])->where('id',$quote_id)->first();
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
                        $html .= '<h3>INVOICES</h3>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-12">';
                        $html .= '<div class="row invoices_div">';
                            if(isset($quote_details->quotes_replys) && count($quote_details->quotes_replys) > 0)
                            {
                                foreach ($quote_details->quotes_replys as $qt_rep)
                                {
                                    $file_path = asset('public/admin_uploads/quote_replies_docs/'.$qt_rep['file']);
                                    $html .= '<div class="col-md-4 text-center mb-2"><a target="_blank" href="'.$file_path.'"  class="btn btn-sm" style="background: #ccc;color: red;"><i class="bi bi-file-pdf"></i> '.$qt_rep['file'].'</a></div>';
                                }
                            }
                            else
                            {
                                $html .= '<div class="col-md-12 text-center">Invoices Not Found!</div>';
                            }
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
                                    $html .= '<div class="main_item_price_div">';
                                        $html .= '<label for="price" class="form-label">Items & Price</label>';
                                        $html .= '<div class="row item_price_div item_price_div_1 mb-3">';
                                            $html .= '<div class="col-md-7">';
                                                $html .= '<input type="text" name="price[item][]" class="form-control" placeholder="Enter Item Name">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-4">';
                                                $html .= '<input type="number" name="price[price][]" class="form-control" value="0">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-1">';
                                                $html .= '<button class="btn btn-sm btn-danger" disabled><i class="bi bi-trash"></i></button>';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="row mt-2">';
                                        $html .= '<div class="col-md-12 mb-3">';
                                            $html .= '<a class="btn btn-sm btn-primary" onclick="AddItemPrice()">Add New</a>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="row mt-2">';
                                        $html .= '<div class="col-md-12 mb-3">';
                                            $html .= '<label for="message" class="form-label">Message</label>';
                                            $html .= '<textarea rows="5" name="message" id="message" class="form-control" placeholder="Write Your Message here."></textarea>';
                                        $html .= '</div>';
                                        $html .= '<div class="col-md-12 mb-3">';
                                            $html .= '<a id="btn-quote-reply" onclick="quoteReply()" class="btn btn-success"><i class="bi bi-send"></i> SEND</a>';
                                            $html .= '<button class="btn btn-success" type="button" disabled style="display:none;" id="load-btn-quote-reply"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please Wait...</button>';
                                        $html .= '</div>';
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
            'price.item.*' => 'required',
            'price.price.*' => 'required',
            'message' => 'required',
        ]);

        try {

            $quote_id = $request->quote_id;
            $products = $request->price;
            $message = $request->message;

            $shop_settings = getClientSettings();
            $currency = (isset($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'USD';

            // Quote Details
            $quote_details = CustomerQuote::find($quote_id);
            $details['latestReplyId'] = CustomerQuoteReply::max('id') + 1;
            $to_email = (isset($quote_details['email'])) ? $quote_details['email'] : '';

            $user_details = User::where('id',1)->where('user_type',2)->first();
            $contact_emails = (isset($user_details['contact_emails']) && !empty($user_details['contact_emails'])) ? unserialize($user_details['contact_emails']) : [];
            $from_email = (count($contact_emails) > 0 && isset($contact_emails[0])) ? $contact_emails[0] : '';

            $details['user_details'] = $user_details;
            $details['quote_details'] = $quote_details;
            $details['products'] = $products;
            $details['message'] = $message;
            $details['currency'] = $currency;

            $path = public_path('admin_uploads\quote_replies_docs');
            $details['file_name'] = 'INVOICE_'.time().'_'.$details['latestReplyId'].'.pdf';

            $pdfFilePath = $path . '/' . $details['file_name'];

            $pdf = Pdf::loadView('pdf.quote_reply_pdf', $details)->setOptions(['defaultFont' => 'sans-serif'])->save($pdfFilePath)->stream();

            if(!empty($from_email) && !empty($to_email))
            {
                \Mail::to($to_email)->send(new \App\Mail\QuoteReplyMail($details));
            }

            // Insert Customer Quote Reply
            $customer_quote_reply = new CustomerQuoteReply();
            $customer_quote_reply->quote_id = $quote_id;
            $customer_quote_reply->price = serialize($products);
            $customer_quote_reply->message = $message;
            $customer_quote_reply->file =  $details['file_name'];
            $customer_quote_reply->save();

            $quotes_replys = CustomerQuoteReply::where('quote_id',$quote_id)->get();

            $html = "";
            if(isset($quotes_replys) && count($quotes_replys) > 0)
            {
                foreach ($quotes_replys as $qt_rep)
                {
                    $file_path = asset('public/admin_uploads/quote_replies_docs/'.$qt_rep['file']);
                    $html .= '<div class="col-md-4 text-center mb-2"><a target="_blank" href="'.$file_path.'"  class="btn btn-sm" style="background: #ccc;color: red;"><i class="bi bi-file-pdf"></i> '.$qt_rep['file'].'</a></div>';
                }
            }
            else
            {
                $html .= '<div class="col-md-12 text-center">Invoices Not Found!</div>';
            }

            return response()->json([
                'success' => 1,
                'message' => 'Message has been Sent SuccessFully..',
                'data' => $html,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }
    }
}
