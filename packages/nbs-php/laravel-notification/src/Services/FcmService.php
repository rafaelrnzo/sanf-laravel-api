<?php

namespace NbsPhp\Notification\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AppInstance;
use Kreait\Firebase\Messaging\RawMessageFromArray;

class FcmService implements PushNotificationServiceInterface
{
    private $firebase;

    //TODO INJECT TOKEN REPOSITORY
    public function __construct()
    {
        $this->firebase = (new Factory())->withServiceAccount($this->jsonFilePath());
    }

    private function jsonFilePath(): string
    {
        return config('fcm.key');
    }

    private function getTokenInstance($token): AppInstance
    {
        try {
            $messaging = $this->firebase->createMessaging();
        } catch (\Exception $e) {
            throw new $e;
        }

        return $messaging->getAppInstance($token);
    }

    public function sendToTopic(string $topic, array $data, array $options = [])
    {
        $message = [
            'topic' => $topic,
            'notification' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#notification
                'title' => (string) $data['title'],
                'body' => (string) $data['body'],
            ],
            'data' => array_merge([
                'sound' => 'default',
            ], $data),
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => array_merge([
                        'alert' => array_merge([
                            'sound' => 'default',
                        ], $data),
                        'badge' => 1,
                        'mutable-content' => 1,
                        'sound' => 'default',
                    ], $options),
                ],
            ],
            'webpush' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
                'notification' => [
                    'title' => (string) $data['title'],
                    'body' => (string) $data['body'],
                    'icon' => (string) ($data['icon'] ?? ''),
                ],
                'fcm_options' => [
                    'link' => (string) ($data['link'] ?? ''),
                ],
            ],
        ];

        try {
            $messaging = $this->firebase->createMessaging();
            $messaging->send(new RawMessageFromArray($message));
        } catch (\Exception $e) {
            throw new $e;
        }
    }

    public function sendToDevice(string $token, array $data, array $options = [])
    {
        $message = [
            'token' => $token,
            'notification' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#notification
                'title' => (string) $data['title'],
                'body' => (string) $data['body'],
            ],
            'data' => array_merge([
                'sound' => 'default',
            ], $data),
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => array_merge([
                        'alert' => array_merge([
                            'sound' => 'default',
                        ], $data),
                        'badge' => 1,
                        'mutable-content' => 1,
                        'sound' => 'default',
                    ], $options),
                ],
            ],
            'webpush' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
                'notification' => [
                    'title' => (string) $data['title'],
                    'body' => (string) $data['body'],
                    'icon' => (string) ($data['icon'] ?? ''),
                ],
                'fcm_options' => [
                    'link' => (string) ($data['link'] ?? ''),
                ],
            ],
        ];

        if (strlen($token) > 0) {
            $messaging = $this->firebase->createMessaging();
            $messaging->send(new RawMessageFromArray($message));
        }
    }

    public function subscribeTopic($topic, $token)
    {
        try {
            $messaging = $this->firebase->createMessaging();
            $messaging->subscribeToTopic($topic, $token);
        } catch (\Exception $e) {
            throw new $e;
        }
    }

    public function unsubscribeTopic($topic, $token)
    {
        try {
            $messaging = $this->firebase->createMessaging();
            $messaging->unsubscribeFromTopic($topic, $token);
        } catch (\Exception $e) {
            throw new $e;
        }
    }

    public function isSubscribedToTopic($topic, $token)
    {
        try {
            return $this->getTokenInstance($token)->isSubscribedToTopic($topic);
        } catch (\Exception $e) {
            throw new $e;
        }
    }
}
