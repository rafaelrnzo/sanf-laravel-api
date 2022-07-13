<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentCompleteSignTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'email' => $item->email,
            'name' => Str::title($item->full_name),
        ];
    }
}