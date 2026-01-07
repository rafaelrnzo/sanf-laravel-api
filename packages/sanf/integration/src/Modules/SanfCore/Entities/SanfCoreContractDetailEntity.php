<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreContractDetailEntity extends FlexibleDataTransferObject
{
    public ?string $CUST_ID;
    public ?string $REG_NO;
    public ?string $NO_KONTRAK;
    public ?string $TGL_KONTRAK;
    public ?string $CURR_ID;
    public ?string $STATUS;
    public ?string $STATUS_ID;
    public ?string $TOTAL_PEMBIAYAAN;
    public ?string $INSTALL_AMT;
    public ?string $TENOR;
    public ?string $ANGSURAN_KE;
    public ?string $TAGIHAN_SISA;
    public ?string $TERBAYAR;
    public ?string $JENIS_PEMBIAYAAN;
    public ?string $ID_JENIS_PEMBIAYAAN;
    public ?string $CARA_PEMBIAYAAN;
    public ?string $ID_CARA_PEMBIAYAAN;
    public ?string $RATE_EFF;
    public ?string $TOT_UNIT;
    public ?string $DT_DUE;
    public ?string $TGL_SELESAI;
    public ?string $TOTAL_DENDA;
    public ?string $POKOK_HUTANG;
    public ?string $BUNGA;
    public ?float $DP;
    public ?string $TOTAL_TAGIHAN;
    public ?string $TIPE_PEMBAYARAN_ID;
    public ?string $TIPE_PEMBAYARAN_DESC;
    public ?string $CONTRACT_TYPE_CODE;
    public ?string $ID_SUPPLIER;
    public ?string $NAMA_SUPPLIER;

    public static function fromLowercaseKeys(array $params): self
    {
        $upperCaseParams = [];

        foreach ($params as $key => $value) {
            $upperCaseParams[strtoupper($key)] = $value;
        }

        if (isset($upperCaseParams['DP'])) {
            $upperCaseParams['DP'] = (float) $upperCaseParams['DP'];
        }

        return new self($upperCaseParams);
    }
}
