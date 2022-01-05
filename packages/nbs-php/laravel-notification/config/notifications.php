<?php
return [
    'table_name' => 'notification',

    'routes' => [
        'prefix' => $routePrefix = 'v1/users',

        'namespace' => $namespace = "NbsPhp\\Notifications\\Controllers\\",

        'list' => [
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/topic-subscriptions",
                'name' => 'notification.subscribe-topic',
                'action' => "{$namespace}FcmController@postTopicSubcription",
                'middleware' => [],
            ], [
                'method' => 'delete',
                'uri' => "{$routePrefix}/topic-subscriptions",
                'name' => 'notification.unsubscribe-topic',
                'action' => "{$namespace}FcmController@deleteTopicSubcription",
                'middleware' => [],
            ], [
                'method' => 'put',
                'uri' => "{$routePrefix}/fcm-tokens",
                'name' => 'notification.unsubscribe-topic',
                'action' => "{$namespace}FcmController@putUpdate",
                'middleware' => [],
            ]
        ],
    ],

    "default_icon" => "logos/logo.png",

    // for now unsupported by fcm topic, because fcm topic cannot be handled same as email and database
    // possibility using rules engine for more dynamic and complex target searching
    "targets" => [
        "user" => [
            "type" => "entity"
        ],
        "{foo.user_id}" => [
            "type" => "resource_owner"
        ],
        "foo.create" => [
            "type" => "permission"
        ],
    ],
    "providers" => [
        "database" => [
            "driver" => "database",
            "repository" => \NbsPhp\Notification\Repositories\EloquentUserNotificationRepository::class
        ],
        "pushnotification" => [
            "driver" => "fcm",
            "service" => \NbsPhp\Notification\Services\FcmService::class
        ],
        "email" => [
            "driver" => "email",
            "mailable" => \NbsPhp\Core\Mail\BaseMail::class
        ]
    ],
    "groups" => [
        "group_name" => [
            "notification_type"
        ]
    ],
    "types" => [
//        FooAddedEvent::class => [
//            'data' => [
//                'id' => '{pretreatmentFeedback.id}',
//                'type' => 'pretreatment_feedback_created',
//                'title' => 'Title Message',
//                'body' => 'Body Message',
//                'icon' => 'logos/logo.png',
//                'link' => '',
//                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
//            ],
//            'providers' => ['pushnotification', 'email', 'database'],
//            'targets' => ['{pretreatment.potential_patient_id}'],
//            'metadata' => ['treatment_unread_count']
//        ],
    ],
];
