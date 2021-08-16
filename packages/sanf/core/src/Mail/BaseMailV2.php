<?php

namespace Sanf\Core\Mail;

use NbsPhp\Core\Mail\BaseMail;

class BaseMailV2 extends BaseMail
{
    /**
     * The Markdown template for the message (if applicable).
     *
     * @var string
    **/

    public $markdown = 'mail::ask-us';

    /**
     * Content template for the message (if applicable).
     *
     * @var string
    **/

    public $topic;
    
    public $title;
    
    public $message;

    public $name;

    public $email;

    public $phone_number;

    public $contract_no;

    public $contact_media;

    public $contact_time;

    public function writeInto($data = [])
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }

        return $this;
    }
    
    public function toArray()
    {
        return [
            'topic' => $this->topic,
            'title' => $this->title,
            'message' => $this->message,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'contract_no' => $this->contract_number,
            'contact_media' => $this->contact_media,
            'contact_time' => $this->contact_time,
        ];
    }

    /**
     * Build the message
     * @todo handle this method if use third party email provider
     */
    public function build()
    {
    }
}
