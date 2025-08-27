<?php

declare(strict_types=1);

namespace EsfahanAhan\Money\Contracts;

use Brick\Math\BigNumber;

interface ICurrency
{
    /**
     * The numeric code of the currency.
     */
    public function getId(): int;

    /**
     * Get the currency name.
     *
     * @return string example: `US Dollar`, `Euro`, `Iranian Rial`, etc
     *
     * @see https://en.wikipedia.org/wiki/List_of_circulating_currencies
     * @see https://en.wikipedia.org/wiki/ISO_4217
     */
    public function getName(): string;

    /**
     * Get the currency code.
     *
     * @return non-empty-string example: `USD`, `EUR`, `IRR`, etc
     */
    public function getCode(): string;

    /**
     * Get the currency symbol.
     *
     * @return string example: `$`, `€`, `﷼`, etc
     *
     * @see https://en.wikipedia.org/wiki/Currency_symbol
     * @see https://en.wikipedia.org/wiki/List_of_currency_symbols
     */
    public function getSymbol(): string;

    /**
     * Get the number of decimal places used by the currency.
     *
     * @return non-negative-int
     *
     * @see https://en.wikipedia.org/wiki/ISO_4217#Currency_decimal_mark
     */
    public function getDecimal(): int;

    /**
     * Get the decimal separator used by the currency.
     *
     * @return string Example: `.`, `,`, etc.
     *
     * @see https://en.wikipedia.org/wiki/Decimal_mark
     */
    public function getDecimalSeparator(): string;

    /**
     * Get the group separator used by the currency.
     *
     * @return non-empty-string Example: `.`, `,`, etc.
     *
     * @see https://en.wikipedia.org/wiki/Decimal_mark
     */
    public function getGroupSeparator(): string;

    /**
     * Format the given money according to the currency's rules.
     */
    public function format(BigNumber|int|float|string $money): string;

    public function isSameAs(ICurrency $other): bool;
}
