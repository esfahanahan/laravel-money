<?php

declare(strict_types=1);

namespace EsfahanAhan\Money\Models;

use Brick\Math\BigNumber;
use EsfahanAhan\Money\Contracts\ICurrency;
use EsfahanAhan\Money\Database\Factories\CurrencyFactory;
use EsfahanAhan\Money\Enums\CurrencyPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int              $id
 * @property non-empty-string $code              The code of the currency, e.g., `USD`, `EUR`, `IRR`.
 * @property string           $name              The name of the currency, e.g., `US Dollar`, `Euro`, `Iranian Rial`.
 * @property string           $symbol            The symbol of the currency, e.g., `$`, `€`, `﷼`.
 * @property non-negative-int $decimal           The number of decimal places used by the currency.
 * @property non-empty-string $group_separator   The group separator used by the currency, e.g., `,`, `.`.
 * @property non-empty-string $decimal_separator The decimal separator used by the currency, e.g., `.`, `,`.
 * @property CurrencyPosition $currency_position The position of the currency symbol relative to the amount, e.g., `left`, `right`, `left-with-space`, `right-with-space` or `hidden`.
 */
class Currency extends Model implements ICurrency
{
    /**
     * @use HasFactory<CurrencyFactory>
     */
    use HasFactory;

    protected static string $factory = CurrencyFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal',
        'group_separator',
        'decimal_separator',
        'currency_position',
    ];

    /**
     * @var array<string,class-string>
     */
    protected $casts = [
        'currency_position' => CurrencyPosition::class,
    ];

    /**
     * Set currency code in capital letter.
     */
    public function setCodeAttribute(string $code): void
    {
        $this->attributes['code'] = strtoupper($code);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getDecimal(): int
    {
        return $this->decimal;
    }

    public function getDecimalSeparator(): string
    {
        return $this->decimal_separator;
    }

    public function getGroupSeparator(): string
    {
        return $this->group_separator;
    }

    public function format(BigNumber|int|float|string $amount): string
    {
        $amount = BigNumber::of($amount);

        $formattedAmount = number_format(
            $amount->toFloat(),
            $this->decimal,
            $this->decimal_separator,
            $this->group_separator
        );

        if ($this->decimal > 0) {
            $formattedAmount = rtrim($formattedAmount, '0');
            $formattedAmount = rtrim($formattedAmount, $this->decimal_separator);
        }

        return match ($this->currency_position) {
            CurrencyPosition::HIDDEN => $formattedAmount,
            CurrencyPosition::LEFT => "{$this->symbol}{$formattedAmount}",
            CurrencyPosition::LEFT_WITH_SPACE => "{$this->symbol} {$formattedAmount}",
            CurrencyPosition::RIGHT => "{$formattedAmount}{$this->symbol}",
            CurrencyPosition::RIGHT_WITH_SPACE => "{$formattedAmount} {$this->symbol}",
        };
    }

    public function isSameAs(ICurrency $other): bool
    {
        if ($other instanceof self) {
            return $this->is($other);
        }

        return $this->getId() === $other->getId()
            && $this->getCode() === $other->getCode();
    }
}
