<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data;
    public $domain;
    public $email;

    public function __construct($data, $domain, $email='')
    {
        $this->data = $data;
        $this->domain = $domain;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

        $encrypted = $this->my_encrypt($this->email, $key);

        if (isset($this->data['subject']) && $this->data['subject'])
            return $this->subject($this->data['subject'])->view('emails.send')->with('domain', $this->domain)->with('encrypted', $encrypted)->with('agent_user_id', $this->data['agent_user_id'])->replyTo($this->data['useremail'], $this->data['useremail']);
        else    
            return $this->view('emails.send')->with('domain', $this->domain)->with('encrypted', $encrypted)->with('agent_user_id', $this->data['agent_user_id'])->replyTo($this->data['useremail'], $this->data['useremail']);
    }

    public function my_encrypt($data, $key) {
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }
}
