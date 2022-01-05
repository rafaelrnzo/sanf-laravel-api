<?php

namespace NbsPhp\Notification\Services;

interface PushNotificationServiceInterface
{
    public function sendToDevice(string $token, array $data, array $options = []);

    public function sendToTopic(string $topic, array $data, array $options = []);

    public function subscribeTopic($topic, $token);

    public function unsubscribeTopic($topic, $token);

    public function isSubscribedToTopic($topic, $token);
}
