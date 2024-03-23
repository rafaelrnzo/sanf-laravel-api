<?php

namespace NbsPhp\Notification\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Notification\Services\NotificationService;
use NbsPhp\Notification\Transformers\MetadataNotificationTransformer;
use NbsPhp\Notification\Transformers\NotificationTransformer;

class NotificationController extends RestApiController
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function metadata(Guard $auth)
    {
        $metadata = $this->service->notificationCount($auth->id());

        return fractal($metadata, new MetadataNotificationTransformer);
    }

    public function markAsRead(Guard $auth, Request $request)
    {
        $input = $this->validate($request, [
            'ids' => 'nullable',
        ]);
        //WRAP TRANSACTION
        DB::transaction(function () use ($auth, $input) {
            $this->service->markNotificationAsRead($auth->id(), $input);
        });

        return $this->responseOk();
    }

    public function listNotifications(Guard $auth, Request $request)
    {
        $input = $this->validate($request, [
            'last_id' => 'string',
            'limit' => 'numeric',
            'group' => 'string',
        ]);
        $notifications = $this->service->getUnreadUserNotifications($auth->id(), $input);

        return fractal($notifications, new NotificationTransformer);
    }

    public function index(Request $request, Guard $auth)
    {
        $input = $this->validate($request, [
            'last_id' => 'string',
            'limit' => 'numeric',
        ]);

        $notifications = $this->service->getUserNotifications($auth->id(), $input);

        return fractal($notifications, new NotificationTransformer);
    }

    public function store(Request $request, Guard $auth)
    {
        $input = $this->validate($request, [
            'type' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'icon' => 'nullable|url',
            'click_action' => 'nullable|string',
        ]);

        $this->service->insertUserNotifications($auth->id(), $input);

        return $this->responseOk();
    }
}
