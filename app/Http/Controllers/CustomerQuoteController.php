<?php

namespace App\Http\Controllers;

use App\Models\CustomerQuote;
use App\Models\CustomerQuoteReply;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CustomerQuoteController extends Controller
{

    // Function for Get All Customer Quotes
    function index()
    {
        $data['customer_quotes'] = CustomerQuote::oldest()->get();
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

            $default_message = $this->defaultQuoteMailMessage();

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
                                    $quote_path = asset('public/admin_uploads/quote_replies_docs/'.$qt_rep['quote_file']);

                                    $html .= '<div class="col-md-4 text-center mb-2">';
                                        $html .= '<div class="position-relative pdf-btn">';

                                            $html .= '<a class="btn btn-sm btn-primary" style="position: absolute;top: 5px; left: 5px;" onclick="editQuoteReply('.$qt_rep['id'].')"><i class="bi bi-pencil"></i></a>';

                                            $html .= '<a class="btn btn-sm btn-primary" style="position: absolute;top: 5px; left: 45px;" onclick="sendInvoice('.$qt_rep['id'].')"><i class="bi bi-receipt"></i></a>';

                                            $html .= '<a target="_blank" href="'.$quote_path.'"  class="btn btn-sm d-block" style="background: #ccc;color: red; padding:15px;"><i class="bi bi-file-pdf"></i> '.$qt_rep['quote_file'].'</a>';

                                        $html .= '</div>';
                                    $html .= '</div>';
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
                        $html .= '<a onclick="resetQuoteReplyForm()" class="btn btn-sm btn-danger mb-3"><i></i>Reset</a>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-12">';
                        $html .= '<div class="card mb-4">';
                            $html .= '<div class="card-body">';
                                $html .= '<form id="quoteReplyForm" enctype="multipart/form-data">';
                                    $html .= csrf_field();
                                    $html .= '<input type="hidden" name="quote_id" id="quote_id" value="'.$quote_id.'">';
                                    $html .= '<div class="main_item_price_div">';
                                        $html .= '<div class="row mb-3">';
                                            $html .= '<div class="col-md-5">';
                                                $html .= '<strong>Item Name</strong>';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-2">';
                                                $html .= '<strong>Qty.</strong>';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-2">';
                                                $html .= '<strong>Price</strong>';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-2">';
                                                $html .= '<strong>Discount</strong>';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                        $html .= '<div class="row item_price_div item_price_div_1 mb-3">';
                                            $html .= '<div class="col-md-5">';
                                                $html .= '<input type="text" name="price[item][]" class="form-control" placeholder="Enter Item Name">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-2">';
                                                $html .= '<input type="number" name="price[qty][]" class="form-control" value="1">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-2">';
                                                $html .= '<input type="number" name="price[price][]" class="form-control" value="0">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-2">';
                                                $html .= '<input type="number" name="price[discount][]" class="form-control" value="0">';
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
                                            $html .= '<textarea rows="5" name="message" id="message" class="form-control" placeholder="Write Your Message here.">'.$default_message.'</textarea>';
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


    // Function for Customer Quote Mail Default Message
    function defaultQuoteMailMessage()
    {
        $default_message = '<p><strong>Dear [customer_name],</strong></p><p>We trust this message finds you well and that you\'re having a great day. Thank you for considering Mahantam Laser Crafts for your upcoming project. We are pleased to provide you with a customized quote based on your requirements.</p><p>Please review the attached quote for a detailed breakdown of the costs and specifications. If you have any questions or need further clarification on any aspect of the quote, our team is here to assist you.</p><p>If you have any additional requirements or would like to discuss any specific details, please do not hesitate to get in touch with us.</p><p>Thank you for considering Mahantam Laser Crafts for your project needs. We value your interest in our products and services.</p><p>Best regards,</p><p>Ronak Vaidh<br>Mahantam Laser Crafts<br>📧 admin@mahantamlasercrafts.com.au<br>🌐 www.mahantamlasercrafts.com.au&nbsp;</p>';

        return $default_message;
    }


    // Function for Send Invoice to Customer
    function sendInvoice(Request $request)
    {
        try{

            $quote_reply_id = $request->quote_reply_id;
            $quote_reply_details = CustomerQuoteReply::with(['customer_quote'])->find($quote_reply_id);

            $shop_settings = getClientSettings();
            $currency = (isset($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'AUD';

            $quote_details = (isset($quote_reply_details['customer_quote'])) ? $quote_reply_details['customer_quote'] : '';

            $user_details = User::where('id',1)->where('user_type',1)->first();
            $contact_emails = (isset($user_details['contact_emails']) && !empty($user_details['contact_emails'])) ? unserialize($user_details['contact_emails']) : [];

            $to_email = (isset($quote_details['email'])) ? $quote_details['email'] : '';
            $from_email = (count($contact_emails) > 0 && isset($contact_emails[0])) ? $contact_emails[0] : '';


            $details['user_details'] = $user_details;
            $details['quote_details'] = $quote_details;
            $details['products'] = (isset($quote_reply_details['price']) && !empty($quote_reply_details['price'])) ? unserialize($quote_reply_details['price']) : [];
            $details['message'] = (isset($quote_reply_details['message'])) ? $quote_reply_details['message'] : '';
            $details['currency'] = $currency;
            $details['to_email'] = $to_email;
            $details['file_name'] = (isset($quote_reply_details['invoice_file'])) ? $quote_reply_details['invoice_file'] : '';
            $details['doc_name'] = 'Invoice';


            if(!empty($from_email) && !empty($to_email))
            {
                // Notify to Customer
                \Mail::to($to_email)->send(new \App\Mail\QuoteReplyMail($details));

                // Notify to Admin
                \Mail::to(env('MAIL_USERNAME'))->send(new \App\Mail\QuoteReplyToAdmin($details));
            }

            return response()->json([
                'success' => 1,
                'message' => 'Invoice has been Sent Successfully..',
            ]);

        }catch (\Throwable $th) {
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
            $quote_reply_id = (isset($request->reply_id) && !empty($request->reply_id)) ? $request->reply_id : '';

            $shop_settings = getClientSettings();
            $currency = (isset($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'USD';

            // Quote Details
            $quote_details = CustomerQuote::find($quote_id);
            $details['latestReplyId'] = CustomerQuoteReply::max('id') + 1;
            $to_email = (isset($quote_details['email'])) ? $quote_details['email'] : '';

            $user_details = User::where('id',1)->where('user_type',1)->first();
            $contact_emails = (isset($user_details['contact_emails']) && !empty($user_details['contact_emails'])) ? unserialize($user_details['contact_emails']) : [];
            $from_email = (count($contact_emails) > 0 && isset($contact_emails[0])) ? $contact_emails[0] : '';

            $details['user_details'] = $user_details;
            $details['quote_details'] = $quote_details;
            $details['products'] = $products;
            $details['message'] = $message;
            $details['currency'] = $currency;
            $details['to_email'] = $to_email;

            $upload_path = public_path('admin_uploads/quote_replies_docs');

            $new_docs['invoice_file'] = 'INVOICE_'.time().'_'.$details['latestReplyId'].'.pdf';
            $new_docs['quote_file'] = 'QUOTE_'.time().'_'.$details['latestReplyId'].'.pdf';
            $new_doc_type['invoice_file'] = 'Invoice';
            $new_doc_type['quote_file'] = 'Quote';

            if(count($new_docs) > 0){
                foreach($new_docs as $key=> $new_doc){
                    $details['file_name'] = $new_doc;
                    $details['doc_name'] = (isset($new_doc_type[$key])) ? $new_doc_type[$key] : '';

                    $file_path = $upload_path. '/' . $details['file_name'];

                    $pdf = Pdf::loadView('pdf.quote_reply_pdf', $details)->setOptions(['defaultFont' => 'sans-serif'])->save($file_path)->stream();
                }
            }

            if(!empty($from_email) && !empty($to_email))
            {
                // Notify to Customer
                \Mail::to($to_email)->send(new \App\Mail\QuoteReplyMail($details));

                // Notify to Admin
                \Mail::to(env('MAIL_USERNAME'))->send(new \App\Mail\QuoteReplyToAdmin($details));
            }

            if(!empty($quote_reply_id)){
                $customer_quote_reply = CustomerQuoteReply::find($quote_reply_id);

                $quote_pdf = (isset($customer_quote_reply['quote_file'])) ? $customer_quote_reply['quote_file'] : '';
                $invoice_pdf = (isset($customer_quote_reply['invoice_file'])) ? $customer_quote_reply['invoice_file'] : '';

                // Delete Old Quote PDF
                if(!empty($quote_pdf) && file_exists('public/admin_uploads/quote_replies_docs/'.$quote_pdf)){
                    unlink('public/admin_uploads/quote_replies_docs/'.$quote_pdf);
                }

                // Delete Old Invoice PDF
                if(!empty($invoice_pdf) && file_exists('public/admin_uploads/quote_replies_docs/'.$invoice_pdf)){
                    unlink('public/admin_uploads/quote_replies_docs/'.$invoice_pdf);
                }

                $customer_quote_reply->price = serialize($products);
                $customer_quote_reply->message = $message;
                $customer_quote_reply->quote_file =  $new_docs['quote_file'];
                $customer_quote_reply->invoice_file =  $new_docs['invoice_file'];
                $customer_quote_reply->update();

            }else{
                // Insert Customer Quote Reply
                $customer_quote_reply = new CustomerQuoteReply();
                $customer_quote_reply->quote_id = $quote_id;
                $customer_quote_reply->price = serialize($products);
                $customer_quote_reply->message = $message;
                $customer_quote_reply->quote_file = $new_docs['quote_file'];
                $customer_quote_reply->invoice_file =  $new_docs['invoice_file'];
                $customer_quote_reply->save();
            }

            $quotes_replys = CustomerQuoteReply::where('quote_id',$quote_id)->get();

            $html = "";
            if(isset($quotes_replys) && count($quotes_replys) > 0)
            {
                foreach ($quotes_replys as $qt_rep)
                {
                    $quote_path = asset('public/admin_uploads/quote_replies_docs/'.$qt_rep['quote_file']);
                    $html .= '<div class="col-md-4 text-center mb-2">';
                        $html .= '<div class="position-relative pdf-btn">';

                            $html .= '<a class="btn btn-sm btn-primary" style="position: absolute;top: 5px; left: 5px;" onclick="editQuoteReply('.$qt_rep['id'].')"><i class="bi bi-pencil"></i></a>';

                            $html .= '<a class="btn btn-sm btn-primary" style="position: absolute;top: 5px; left: 45px;" onclick="sendInvoice('.$qt_rep['id'].')"><i class="bi bi-receipt"></i></a>';

                            $html .= '<a target="_blank" href="'.$quote_path.'"  class="btn btn-sm" style="background: #ccc;color: red; padding:15px;"><i class="bi bi-file-pdf"></i> '.$qt_rep['quote_file'].'</a>';

                        $html .= '</div>';
                    $html .= '</div>';
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


    // Function for Edit Quote Reply
    function quoteReplyEdit(Request $request){
        try
        {
            $quote_reply_id = $request->quote_reply_id;
            $quote_rep_dt = CustomerQuoteReply::find($quote_reply_id);
            $items_arr = (isset($quote_rep_dt['price']) && !empty($quote_rep_dt['price'])) ? unserialize($quote_rep_dt['price']) : [];
            $html = '';

            $html .= '<div class="row mb-3">';
                $html .= '<div class="col-md-5">';
                    $html .= '<strong>Item Name</strong>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                    $html .= '<strong>Qty.</strong>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                    $html .= '<strong>Price</strong>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                    $html .= '<strong>Discount</strong>';
                $html .= '</div>';
            $html .= '</div>';

            if(isset($items_arr) && count($items_arr) > 0){
                $items = (isset($items_arr['item'])) ? $items_arr['item'] : '';
                $prices = (isset($items_arr['price'])) ? $items_arr['price'] : '';
                $quantities = (isset($items_arr['qty'])) ? $items_arr['qty'] : '';
                $discounts = (isset($items_arr['discount'])) ? $items_arr['discount'] : '';

                if(count($items) > 0)
                {
                    foreach ($items as $key => $item){

                        $loop_itr = $key + 1;
                        $price = (isset($prices[$key])) ? $prices[$key] : 0;
                        $discount = (isset($discounts[$key])) ? $discounts[$key] : 0;
                        $quantity = (isset($quantities[$key])) ? $quantities[$key] : 0;

                        $html .= '<input type="hidden" name="reply_id" id="reply_id" value="'.$quote_reply_id.'">';

                        if($loop_itr == 1){
                            $html .= '<div class="row item_price_div item_price_div_'.$loop_itr.' mb-3">';
                        }else{
                            $html .= '<div class="row item_child item_price_div item_price_div_'.$loop_itr.' mb-3">';
                        }
                            $html .= '<div class="col-md-5">';
                                $html .= '<input type="text" name="price[item][]" class="form-control" placeholder="Enter Item Name" value="'.$item.'">';
                            $html .= '</div>';
                            $html .= '<div class="col-md-2">';
                                $html .= '<input type="number" name="price[qty][]" class="form-control" value="'.$quantity.'">';
                            $html .= '</div>';
                            $html .= '<div class="col-md-2">';
                                $html .= '<input type="number" name="price[price][]" class="form-control" value="'.$price.'">';
                            $html .= '</div>';
                            $html .= '<div class="col-md-2">';
                                $html .= '<input type="number" name="price[discount][]" class="form-control" value="'.$discount.'">';
                            $html .= '</div>';
                            $html .= '<div class="col-md-1">';
                                if($loop_itr == 1){
                                    $html .= '<button class="btn btn-sm btn-danger" disabled><i class="bi bi-trash"></i></button>';
                                }else{
                                    $html .= '<a onclick="$(\'.item_price_div_'.$loop_itr.'\').remove()" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>';
                                }
                            $html .= '</div>';
                        $html .= '</div>';
                    }
                }
            }

            return response()->json([
                'success' => 1,
                'data' => $html,
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
}
