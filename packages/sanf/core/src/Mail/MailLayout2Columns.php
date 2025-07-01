<?php

namespace Sanf\Core\Mail;

use NbsPhp\Core\Mail\BaseMail;

class MailLayout2Columns extends BaseMail
{
    /**
     * The Markdown template for the message (if applicable).
     *
     * @var string
     **/
    public $markdown = 'mail::message-v3';

    /**
     * Content template for the message (if applicable).
     *
     * @var array
     **/
    public $emailContent = [];

    /**
     * Table header template for the message (if applicable).
     *
     * @var array
     **/
    public $emailTableHeader = [];

    /**
     * Table body template for the message (if applicable).
     *
     * @var array
     **/
    public $emailTableBody = [];

    /**
     * Bank sections for the message (if applicable).
     *
     * @var array
     **/
    public $bankSections = [];

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

    public function generateSeparator($separator = [])
    {
        foreach ($separator as $value) {
            array_splice(
                $this->emailContent,
                $value['joinToIndex'],
                0,
                [
                    [
                        'separator' => $value['html'],
                    ],
                ]
            );
        }

        return $this;
    }

    public function writeContent($content = [])
    {
        $tempArr = [];

        foreach ($content as $key => $value) {
            array_push($tempArr, [
                'label' => $key,
                'text'  => $value,
            ]);
        }

        $this->emailContent = $tempArr;

        return $this;
    }

    public function writeTableHead($th = [])
    {
        $this->emailTableHeader = $th;

        return $this;
    }

    public function writeTableBody($tb = [])
    {
        $this->emailTableBody = $tb;

        return $this;
    }

    public function writeBankSections($sections = [])
    {
        $this->bankSections = $sections;

        return $this;
    }

    public function toArray()
    {
        return [
            'content'      => $this->emailContent,
            'tableHead'    => $this->emailTableHeader,
            'tableBody'    => $this->emailTableBody,
            'bankSections' => $this->bankSections,
        ];
    }

    /**
     * Build the message.
     * @todo handle this method if use third party email provider
     */
    public function build()
    {
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
}
