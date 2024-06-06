<?php

namespace Sanf\External\Modules\Financing\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Financing\Transformers\FinancingApplicationSimpleTransformer;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationObjectByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Dto\FinancingApplicationPaymentByScaninaRequestDto;
use Sanf\Core\Modules\Financing\Services\SubmitFinanceApplicationByScaninaUseCase;
use Spatie\Fractalistic\ArraySerializer;

class FinancingApplicationController extends RestApiController
{
    public function addByScanina(Request $request, SubmitFinanceApplicationByScaninaUseCase $submitUseCase)
    {
        $inputs = $this->validate($request, [
            'profile_xid' => ['required', 'alpha_num', 'max: 13'],
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
            'payment' => new FinancingApplicationPaymentByScaninaRequestDto([
                'amount' => $inputs['payment']['amount'],
                'down_payment_percentage' => $inputs['payment']['down_payment_percentage'],
                'down_payment_amount' => $inputs['payment']['down_payment_amount'],
                'first_payment_amount' => $inputs['payment']['first_payment_amount'],
                'total_amount' => $inputs['payment']['total_amount'],
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
                    'price_per_unit' => $object['price_per_unit'],
                ]);
            }, $inputs['objects']),
        ]);

        $responseDto = $submitUseCase->execute($requestDto);

        return fractal($responseDto)
            ->transformWith(FinancingApplicationSimpleTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
