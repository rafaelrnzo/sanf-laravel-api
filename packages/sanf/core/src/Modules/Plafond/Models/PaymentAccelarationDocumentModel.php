<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;

class PaymentAccelarationDocumentModel extends AbstractModel
{
    protected $table = 'payment_acc_document';

    protected $fillable = [
        'xid',
        'client_id',
        'plafond_id',
        'company',
        'bowheer',
        'bowheer_email',
        'document_no',
        'document_date',
        'first_signer_company',
        'first_signer_name',
        'first_signer_position',
        'second_signer_company',
        'second_signer_name',
        'second_signer_position',
        'origin',
        'filename',
        'path',
        'metadata',
        'invoices',
    ];
}
