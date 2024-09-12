<?php

namespace Sanf\External\Modules\Contract;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Core\Modules\Contract\Enums\AdInsCallbackTypeEnum;
use Sanf\Core\Modules\Contract\Events\ESignDocumentSignEvent;
use Sanf\Core\Modules\Contract\Services\ESignAdInsCallbackService;

class ESignDocumentAdInsController extends RestApiController
{
    protected TransactionalSessionInterface $transactionalSession;
    protected ESignAdInsCallbackService $callbackService;

    public function __construct(
        TransactionalSessionInterface $transactionalSession,
        ESignAdInsCallbackService $callbackService
    ) {
        $this->transactionalSession = $transactionalSession;
        $this->callbackService = $callbackService;
    }

    public function callback(Request $request)
    {
        $input = $this->validate($request, [
            'message' => [
                'required',
                'string',
            ],
            'callbackType' => [
                'required',
                Rule::in([
                    AdInsCallbackTypeEnum::ACTIVATION_COMPLETE,
                    AdInsCallbackTypeEnum::SIGNING_COMPLETE,
                    AdInsCallbackTypeEnum::DOCUMENT_SIGN_COMPLETE,
                    AdInsCallbackTypeEnum::ALL_DOCUMENT_SIGN_COMPLETE,
                ]),
            ],
            'data' => [
                'required',
            ],
            'data.email' => [
                'email',
                Rule::requiredIf(function () use ($request) {
                    return in_array($request->callbackType, [
                        AdInsCallbackTypeEnum::ACTIVATION_COMPLETE,
                        AdInsCallbackTypeEnum::SIGNING_COMPLETE,
                    ]);
                }),
            ],
            'data.phone' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->callbackType === AdInsCallbackTypeEnum::ACTIVATION_COMPLETE;
                }),
                'string',
                'max:16',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(\+62|62|0)/', $value)) {
                        return $fail('The phone number must start with +62, 62, or 0.');
                    }
                    if (!preg_match('/^\+?[0-9]+$/', $value)) {
                        return $fail('The phone number must only contain numeric characters.');
                    }
                },
            ],
            'data.documentId' => [
                'string',
                'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/',
                Rule::requiredIf(function () use ($request) {
                    return in_array($request->callbackType, [
                        AdInsCallbackTypeEnum::SIGNING_COMPLETE,
                        AdInsCallbackTypeEnum::DOCUMENT_SIGN_COMPLETE,
                    ]);
                }),
            ],
            'data.refNo' => [
                'string',
                'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/',
                Rule::requiredIf(function () use ($request) {
                    return in_array($request->callbackType, [
                        AdInsCallbackTypeEnum::ALL_DOCUMENT_SIGN_COMPLETE,
                    ]);
                }),
            ],
        ]);

        if ($input['callbackType'] === AdInsCallbackTypeEnum::ACTIVATION_COMPLETE) {
            $dto = (object) [
                'email' => strtolower($input['data']['email']),
                'phone' => $input['data']['phone'],
            ];
        }

        if ($input['callbackType'] === AdInsCallbackTypeEnum::SIGNING_COMPLETE) {
            $dto = (object) [
                'email' => strtolower($input['data']['email']),
                'documentId' => $input['data']['documentId'],
            ];
        }

        if ($input['callbackType'] === AdInsCallbackTypeEnum::DOCUMENT_SIGN_COMPLETE) {
            $dto = (object) [
                'documentId' => $input['data']['documentId'],
            ];
        }

        if ($input['callbackType'] === AdInsCallbackTypeEnum::ALL_DOCUMENT_SIGN_COMPLETE) {
            $dto = (object) [
                'refNo' => $input['data']['refNo'],
            ];
        }
        $dto->callbackType = $input['callbackType'];

        $transactionalService = new TransactionalApplicationService($this->callbackService, $this->transactionalSession);
        $transactionalService->execute($dto);

        return response()->json([
            'status' => [
                'code' => 0,
                'message' => 'Success',
            ],
        ]);
    }

    public function check(Request $request)
    {
        $input = $this->validate($request, [
            'userId' => 'required|string|max:255',
            'sanfId' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'string',
                'max:255',
            ],
            'msisdn' => [
                'required',
                'string',
                'max:16',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(\+62|62|0)/', $value)) {
                        return $fail('The phone number must start with +62, 62, or 0.');
                    }
                    if (!preg_match('/^\+?[0-9]+$/', $value)) {
                        return $fail('The phone number must only contain numeric characters.');
                    }
                },
            ],
            'documentId' => 'required|string|max:255',
            'referenceNo' => 'required|string|max:255',
        ]);

        $dto = (object) $input;

        event(new ESignDocumentSignEvent($dto));

        return response()->json([
            'status' => [
                'code' => 0,
                'message' => 'Success',
            ],
        ]);
    }
}
