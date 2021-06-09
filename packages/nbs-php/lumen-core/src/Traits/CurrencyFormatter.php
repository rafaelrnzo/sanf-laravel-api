<?php

namespace NbsPhp\Core\Traits;

trait CurrencyFormatter
{
    /**
     * Format the given amount into a displayable currency.
     *
     * @param int $amount
     * @param string|null $locale
     * @param string|null $currency
     * @return string
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public function formatCurrency($amount, $locale = null, $currency = null): string
    {
        $formatter = new \NumberFormatter($locale ?? config('core.currency_locale'), \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($amount, $currency ?? config('core.currency_code'));
    }
}
