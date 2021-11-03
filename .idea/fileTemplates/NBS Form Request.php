<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end

use Illuminate\Foundation\Http\FormRequest;

class ${NAME}Request extends FormRequest
{
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'total' => ['nullable', 'integer', 'max:2147483647'],
            'price' => ['nullable', 'numeric', 'max:999999999999999.9999'],
            'is_enabled' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'created_at' => ['nullable', 'integer', 'max:99999999999],
        ];
    }

}