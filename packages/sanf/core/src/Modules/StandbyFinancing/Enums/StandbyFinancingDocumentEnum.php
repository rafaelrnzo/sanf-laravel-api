<?php

namespace Sanf\Core\Modules\StandbyFinancing\Enums;

final class StandbyFinancingDocumentEnum
{
    public const VALIDATION = '002';
    public const OTHER = '999';

    public const RULES = [
        [
            'doc_id' => self::VALIDATION,
            'doc_desc' => 'Validasi',
            'required' => true,
        ],
        [
            'doc_id' => self::OTHER,
            'doc_desc' => 'Other Document',
            'required' => false,
        ],
    ];
}
