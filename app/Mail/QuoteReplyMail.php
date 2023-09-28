<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('mail_username'), 'Mahantam Laser Crafts')->attach(public_path('admin_uploads/quote_replies_docs/'.$this->details['file_name']), [
            'as' => $this->details['file_name'], // Attachment file name
            'mime' => 'application/pdf', // MIME type
        ])->subject('Customer Quotation')->view('mails.customer_quote_reply');
    }
}
