<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;

class ESignDocumentAssigneeModel extends AbstractModel
{
    protected $table = 'esign_document_assignee';

    public function eSignDocument()
    {
        return $this->hasOne(ESignDocumentModel::class, 'document_id', 'document_id');
    }
}
