<?php

namespace Sanf\External\Modules\Financing\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Api\Modules\Financing\Transformers\FinancingApplicationSimpleTransformer;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationObjectByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationPaymentByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Services\SubmitFinanceApplicationByScaninaUseCase;
use Spatie\Fractalistic\ArraySerializer;

class FinancingApplicationController extends RestApiController
{
    public function addByScanina(
        Request $request,
        TransactionalSessionInterface $transactionalSession,
        SubmitFinanceApplicationByScaninaUseCase $submitUseCase
    ) {
        $inputs = $this->validate($request, [
            'profile_xid' => ['required', 'alpha_num', 'max: 13'],
            'is_receive_offer' => ['nullable', 'boolean'],
            'payment.amount' => ['required', 'numeric'],
            'payment.down_payment_percentage' => ['required', 'numeric'],
            'payment.down_payment_amount' => ['required', 'numeric'],
            'payment.first_payment_amount' => ['required', 'numeric'],
            'payment.total_amount' => ['required', 'numeric'],
            'payment.tenor' => ['required', 'integer'],
            'objects' => ['required', 'array'],
            'objects.*.provider' => ['required', 'string', 'max: 128'],
            'objects.*.category' => ['required', 'string', 'max: 128'],
            'objects.*.brand' => ['required', 'string', 'max: 128'],
            'objects.*.type' => ['required', 'string', 'max: 128'],
            'objects.*.model' => ['required', 'string', 'max: 128'],
            'objects.*.description' => ['required', 'string', 'max: 255'],
            'objects.*.quantity' => ['required', 'integer'],
            'objects.*.price_per_unit' => ['required', 'numeric'],
        ]);

        $requestDto = new FinancingApplicationByScaninaRequestDto([
            'profile_xid' => $request->get('profile_xid'),
            'is_receive_offer' => (bool) $request->get('is_receive_offer') ?? false,
            'payment' => new FinancingApplicationPaymentByScaninaRequestDto([
                'amount' => (float) $inputs['payment']['amount'],
                'down_payment_percentage' => (float) $inputs['payment']['down_payment_percentage'],
                'down_payment_amount' => (float) $inputs['payment']['down_payment_amount'],
                'first_payment_amount' => (float) $inputs['payment']['first_payment_amount'],
                'total_amount' => (float) $inputs['payment']['total_amount'],
                'tenor' => $inputs['payment']['tenor'],
            ]),
            'objects' => array_map(function ($object) {
                return new FinancingApplicationObjectByScaninaRequestDto([
                    'provider' => $object['provider'],
                    'category' => $object['category'],
                    'brand' => $object['brand'],
                    'type' => $object['type'],
                    'model' => $object['model'],
                    'description' => $object['description'] ?? null,
                    'quantity' => $object['quantity'],
                    'price_per_unit' => (float) $object['price_per_unit'],
                ]);
            }, $inputs['objects']),
        ]);

        $transactionalService = new TransactionalApplicationService($submitUseCase, $transactionalSession);
        $responseDto = $transactionalService->execute($requestDto);

        return fractal($responseDto)
            ->transformWith(FinancingApplicationSimpleTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
