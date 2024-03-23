<?php

namespace NbsPhp\Notification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use NbsPhp\Core\Enum\EntityType;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Notification\Jobs\InsertDatabaseNotificationJob;
use NbsPhp\Notification\Jobs\SendEmailNotificationJob;
use NbsPhp\Notification\Jobs\SendPushNotificationJob;
use NbsPhp\Notification\Jobs\UpdateMetadataNotificationJob;
use NbsPhp\Notification\Models\NotificationChannelModel;
use NbsPhp\Notification\Models\UserSessionModel;

class NotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        foreach (config('notifications.types') as $eventType => $type) {
            if ($event instanceof $eventType) {
                if (isset($type[0])) { // check if consist of array data
                    foreach ($type as $typeItem) {
                        $data = $typeItem['data'] ?? [];
                        $this->sendNotification($event, $typeItem, $data);
                    }
                } else {
                    $data = $type['data'] ?? [];
                    $this->sendNotification($event, $type, $data);
                }
            }
        }
    }

    protected function sendNotification($event, $type, $data)
    {
        // check if required variable filled
        $providers = $type['providers'] ?? [];
        $targets = $type['targets'] ?? [];
        if (empty($providers) || empty($targets) || empty($data)) {
            return;
        }
        $notifiables = $this->findTargets($event, $targets);
        // SEND NOTIFICATION
        foreach ($providers as $provider) {
            $this->dispatchJobByProvider($provider, $event, $data, $notifiables, $targets);
        }
        // UPDATE METADATA
        $metadata = $type['metadata'] ?? [];
        foreach ($notifiables as $notifiable) {
            //TODO CREATE NOTIFIABLE CLASS
            $notifiable = json_decode(json_encode($notifiable));
            dispatch(new UpdateMetadataNotificationJob($metadata, $event, $notifiable));
        }
    }

    protected function buildTemplateVariables($obj)
    {
        //convert all object properties into dot notation array
        $vars = array_dot(json_decode(json_encode(get_object_vars($obj)), true));
        $varBraces = [];
        //wrap with curly brackets {key}
        foreach ($vars as $key => $value) {
            $varBraces['{' . $key . '}'] = $value;
        }

        return $varBraces;
    }

    //TODO CLASS PAYLOAD BUILDER
    protected function buildPayload($event, $data)
    {
        $templateVars = $this->buildTemplateVariables($event);
        //TODO USE PREGENERATED UNIQUE ID
        $data['id_ext'] = (string) Str::orderedUuid();
        if (isset($data['id'])) {
            $data['id'] = strtr($data['id'], $templateVars);
        }
        $data['title'] = strtr($data['title'], $templateVars);
        $data['body'] = strtr($data['body'], $templateVars);
        $data['body_formatted'] = strtr($data['body_formatted'] ?? $data['body'], $templateVars);
        $data['link'] = strtr($data['link'] ?? '', $templateVars);
        $data['icon'] = file_get_url($data['icon'] ?? config('notifications.default_icon'));

        return $data;
    }

    protected function dispatchJobByProvider($provider, $event, $data, $notifiables, $targets)
    {
        //TODO PARSE PROVIDER
        //TODO IMPROVE FIND TARGETS QUERY OPTIMIZE
        $payload = $this->buildPayload($event, $data);
        switch ($provider) {
            case 'email':
                foreach ($notifiables as $notifiable) {
                    $notifiable = json_decode(json_encode($notifiable));
                    dispatch(new SendEmailNotificationJob($notifiable, $payload));
                }

                return;
            case 'database':
                foreach ($notifiables as $notifiable) {
                    $notifiable = json_decode(json_encode($notifiable));
                    $notifiable->type = 'user'; //TODO SET THIS SOMEWHERE
                    dispatch(new InsertDatabaseNotificationJob($notifiable, $payload));
                }

                return;
            case 'pushnotification':
                // FOR NOW ONLY SUPPORTED FCM
                $fcmTokens = $this->findTargetFcmTokens($event, $targets);
                foreach ($fcmTokens as $fcmToken) {
                    dispatch(new SendPushNotificationJob($fcmToken, $payload));
                }

                return;
            default:
                throw new \Exception('Notification: Undefined Driver ' . $provider);
        }
    }

    protected function findTargetFcmTokens($event, $targets)
    {
        //NEED QUERY IMPROVEMENT TO COLLECT UNIQUE VALUES FROM MULTIPLE TARGET
        $tokens = [];
        foreach ($targets as $target) {
            $configs = config('notifications.targets');
            switch ($configs[$target]['type']) {
                case 'permission':
                    $results = UserSessionModel::select('notification_token')
                        ->where('notification_channel_id', NotificationChannelModel::FCM)
                        ->whereIn('user_id', function ($query) use ($target) {
                            $query->select('id')
                                ->from(with(new AuthModel())->getTable())
                                ->whereJsonContains('permissions', $target);
                        })
                        ->pluck('notification_token')->toArray();
                    $tokens = array_unique(array_merge($tokens, $results));
                    break;
                case 'entity':
                    $entityConst = strtoupper($target);
                    $entityTypeId = EntityType::values()[$entityConst]->getValue();
                    $results = UserSessionModel::select('notification_token')
                        ->where('notification_channel_id', NotificationChannel::FCM)
                        ->whereIn('user_id', function ($query) use ($entityTypeId) {
                            $query->select('id')
                                ->from(with(new AuthModel())->getTable())
                                ->where('entity_type_id', $entityTypeId);
                        })
                        ->pluck('notification_token')->toArray();
                    $tokens = array_unique(array_merge($tokens, $results));
                    break;
                case 'resource_owner':
                    $templateVars = $this->buildTemplateVariables($event);
                    $userId = strtr($target, $templateVars);
                    $results = UserSessionModel::select('notification_token')
                        ->where('notification_channel_id', NotificationChannelModel::FCM)
                        ->whereIn('user_id', function ($query) use ($userId) {
                            $query->select('id')
                                ->from(with(new AuthModel())->getTable())
                                ->where('id', $userId);
                        })
                        ->pluck('notification_token')->toArray();
                    $tokens = array_unique(array_merge($tokens, $results));
                    break;
                default:
                    throw new \Exception('Notification: undefined targets type' . $target['type']);
            }
        }

        return $tokens;
    }

    protected function findTargets($event, $targets)
    {
        //NEED QUERY IMPROVEMENT TO COLLECT UNIQUE VALUES FROM MULTIPLE TARGET
        $notifiables = collect();
        foreach ($targets as $target) {
            $configs = config('notifications.targets');
            switch ($configs[$target]['type']) {
                case 'permission':
                    $results = AuthModel::select('id', 'name', 'username as email')
                        ->where('status_id', 1) //TODO ENUM USER STATUS
                        ->whereJsonContains('permissions', $target)->get()->toArray();
                    $notifiables = $notifiables->merge(collect($results));
                    break;
                case 'entity':
                    $entityConst = strtoupper($target);
                    $entityTypeId = EntityType::values()[$entityConst]->getValue();
                    $results = AuthModel::select('id', 'name', 'username as email')
                        ->where('status_id', 1) //TODO ENUM USER STATUS
                        ->where('entity_type_id', $entityTypeId)->get()->toArray();
                    $notifiables = $notifiables->merge(collect($results));
                    break;
                case 'resource_owner':
                    $templateVars = $this->buildTemplateVariables($event);
                    $userId = strtr($target, $templateVars);
                    $results = AuthModel::select('id', 'name', 'username as email')
                        ->where('id', $userId)->get()->toArray();
                    $notifiables = $notifiables->merge(collect($results));
                    break;
                default:
                    throw new \Exception('Notification: undefined targets type' . $target['type']);
            }
            $notifiables = $notifiables->unique('id');
        }

        return $notifiables;
    }
}
