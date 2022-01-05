<?php

namespace NbsPhp\Notification\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class GenerateFcmJavascriptCommand extends Command
{
    protected $name = 'fcm:generate-js';

    protected $description = 'Generate Firebase Messaging Service Worker';

    protected $file;

    protected $config;

    public function __construct(Filesystem $files, Repository $config)
    {
        parent::__construct();

        $this->file = $files;
        $this->config = $config;
    }

    public function handle()
    {
        if (!$this->config->has('fcm.web_config') || !$this->config->has('fcm.web_push_key')) {
            $this->error('Please set \'FIREBASE_WEB_CONFIG\' and \'FIREBASE_WEB_PUSH_KEY\' in .env file.');

            return;
        }

        // TODO: add option force and check file
        /*if ((!$this->hasOption('force') || !$this->option('force')) && $this->file->exists($fileDest)) {
            $this->error('File [firebase-messaging-sw.js] already exists!');

            return;
        }*/

        $stubFcm = $this->file->get(__DIR__ . '/../../public/fcm.js.stub');
        $stubFcmSw = $this->file->get(__DIR__ . '/../../public/firebase-messaging-sw.js.stub');

        $fcmWebConfig = base64_decode($this->config->get('fcm.web_config'));
        $fcmWebPushKey = $this->config->get('fcm.web_push_key');

        $stubFcm = str_replace('FCM_WEB_CONFIG', $fcmWebConfig, $stubFcm);
        $stubFcm = str_replace('FCM_WEB_PUSH_KEY', "\"{$fcmWebPushKey}\"", $stubFcm);

        $stubFcmSw = str_replace('FCM_WEB_CONFIG', $fcmWebConfig, $stubFcmSw);

        $this->file->put(public_path('fcm.js'), $stubFcm);
        $this->file->put(public_path('firebase-messaging-sw.js'), $stubFcmSw);

        $this->info('Added [firebase-messaging-sw.js] and [fcm.js] successfully.');
    }
}
