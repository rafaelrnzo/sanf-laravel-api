<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentSignedSignTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'email' => $item->email,
            'url' => $item->document_sign_url,
        ];
    }
}