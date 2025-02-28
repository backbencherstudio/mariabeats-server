<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class FranchaiseeRequestCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $franchaisee;

    /**
     * Create a new message instance.
     */
    public function __construct($franchaisee)
    {
        $this->franchaisee = $franchaisee;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->markdown('emails.franchaisee.request-created')
                    ->subject('New Franchaisee Request');
    }
} 