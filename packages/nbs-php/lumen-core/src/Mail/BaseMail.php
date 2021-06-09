<?php

namespace NbsPhp\Core\Mail;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Action;

class BaseMail extends Mailable
{
    /**
     * The Markdown template for the message (if applicable).
     *
     * @var string
     */
    public $markdown = 'mail::message';

    /**
     * The logo image.
     *
     * @var string
     */
    public $logo;

    /**
     * The banner image.
     *
     * @var string
     */
    public $banner;

    /**
     * The notification's greeting.
     *
     * @var string
     */
    public $greeting;

    /**
     * The "intro" lines of the notification.
     *
     * @var array
     */
    public $introLines = [];

    /**
     * The "outro" lines of the notification.
     *
     * @var array
     */
    public $outroLines = [];

    /**
     * The text / label for the action.
     *
     * @var string
     */
    public $actionText;

    /**
     * The action URL.
     *
     * @var string
     */
    public $actionUrl;

    /**
     * The action Help.
     *
     * @var array
     */
    public $actionHelp;

    /**
     * Add a line of text to the notification.
     *
     * @param mixed $line
     *
     * @return $this
     */
    private function withLine($line)
    {
        if ($line instanceof Action) {
            $this->action($line->text, $line->url);
        } elseif (!$this->actionText) {
            $this->introLines[] = $this->formatLine($line);
        } else {
            $this->outroLines[] = $this->formatLine($line);
        }

        return $this;
    }

    /**
     * Format the given line of text.
     *
     * @param Htmlable|string|array $line
     *
     * @return Htmlable|string
     */
    private function formatLine($line)
    {
        if ($line instanceof Htmlable) {
            return $line;
        }

        if (is_array($line)) {
            return implode(' ', array_map('trim', $line));
        }

        return trim(implode(' ', array_map('trim', preg_split('/\\r\\n|\\r|\\n/', $line))));
    }

    /**
     * Set logo
     *
     * @param $logo
     *
     * @return $this
     */
    public function logo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Set banner
     *
     * @param $banner
     *
     * @return $this
     */
    public function banner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Set the greeting of the notification.
     *
     * @param string $greeting
     *
     * @return $this
     */
    public function greeting($greeting)
    {
        $this->greeting = $greeting;

        return $this;
    }

    /**
     * Add a line of text to the notification.
     *
     * @param mixed $line
     * @param null|string $type only accept top and bottom
     *
     * @return $this
     */
    public function line($line, $type = null)
    {
        if ($type) {
            array_push($this->{$type}, $this->formatLine($line));

            return $this;
        }

        return $this->withLine($line);
    }

    /**
     * Configure the "call to action" button.
     *
     * @param string $text
     * @param string $url
     * @param array $help
     *
     * @return $this
     */
    public function action($text, $url, $help = [])
    {
        $this->actionText = $text;
        $this->actionUrl = $url;
        $this->actionHelp = $help;

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'subject' => $this->subject,
            'logo' => $this->logo,
            'banner' => $this->banner,
            'greeting' => $this->greeting,
            'introLines' => $this->introLines,
            'outroLines' => $this->outroLines,
            'actionText' => $this->actionText,
            'actionUrl' => $this->actionUrl,
            'actionHelp' => $this->actionHelp,
            'displayableActionUrl' => str_replace(['mailto:', 'tel:'], '', $this->actionUrl),
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
