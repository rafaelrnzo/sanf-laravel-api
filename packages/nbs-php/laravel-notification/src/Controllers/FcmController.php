<?php

namespace NbsPhp\Notification\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Notification\Models\UserSessionModel;

class FcmController extends RestApiController
{
    public function saveToken(Request $request)
    {
        $input = $this->validate($request, [
            'token' => 'required',
            'auth_provider_id' => 'required|exists:m_auth_provider,id',
            'device_platform_id' => 'required|exists:m_device_platform,id',
            'notification_channel_id' => 'required|exists:m_notification_channel,id',
        ]);

        UserSessionModel::query()->where('notification_token', $input['token'])->delete();
        UserSessionModel::query()->create([
            'user_id' => Auth::id(),
            'auth_provider_id' => $input['auth_provider_id'],
            'device_platform_id' => $input['device_platform_id'],
            'notification_channel_id' => $input['notification_channel_id'],
            'notification_token' => $input['token'],
            'expired_at' => Carbon::now()->addYear(),
        ]);

        return $this->responseOk();
    }

    public function subscribeTopic(Request $request, FcmService $fcm)
    {
        $input = $request->validate([
            'topic' => 'required',
            'token' => 'required',
        ]);

        if ($request->ajax()) {
            try {
                if (!$fcm->isSubscribedToTopic($input['topic'], $input['token'])) {
                    $fcm->subscribeTopic($input['topic'], $input['token']);
                }

                return response()->json([]);
            } catch (\Exception $e) {
                report($e);

                return response()->json([], 500);
            }
        }

        return response()->json([], 405);
    }

    public function unsubscribeTopic(Request $request, FcmService $fcm)
    {
        $input = $request->validate([
            'topic' => 'required',
            'token' => 'required',
        ]);

        if ($request->ajax()) {
            try {
                if ($fcm->isSubscribedToTopic($input['topic'], $input['token'])) {
                    $fcm->unsubscribeTopic($input['topic'], $input['token']);
                }

                return response()->json([]);
            } catch (\Exception $e) {
                report($e);

                return response()->json([], 500);
            }
        }

        return response()->json([], 405);
    }
}
