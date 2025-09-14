<?php

declare(strict_types=1);

namespace EsfahanAhan\Money\Models;

use Brick\Math\BigNumber;
use EsfahanAhan\Money\Contracts\ICurrency;
use EsfahanAhan\Money\Database\Factories\CurrencyFactory;
use EsfahanAhan\Money\Enums\CurrencyPositionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                  $id
 * @property non-empty-string     $code              The code of the currency, e.g., `USD`, `EUR`, `IRR`.
 * @property string               $name              The name of the currency, e.g., `US Dollar`, `Euro`, `Iranian Rial`.
 * @property string               $symbol            The symbol of the currency, e.g., `$`, `€`, `﷼`.
 * @property non-negative-int     $decimal           The number of decimal places used by the currency.
 * @property non-empty-string     $group_separator   The group separator used by the currency, e.g., `,`, `.`.
 * @property non-empty-string     $decimal_separator The decimal separator used by the currency, e.g., `.`, `,`.
 * @property CurrencyPositionEnum $currency_position The position of the currency symbol relative to the amount, e.g., `left`, `right`, `left-with-space`, `right-with-space`.
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
        'currency_position' => CurrencyPositionEnum::class,
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

        return match ($this->currency_position) {
            CurrencyPositionEnum::LEFT => "{$this->symbol}{$formattedAmount}",
            CurrencyPositionEnum::LEFT_WITH_SPACE => "{$this->symbol} {$formattedAmount}",
            CurrencyPositionEnum::RIGHT => "{$formattedAmount}{$this->symbol}",
            CurrencyPositionEnum::RIGHT_WITH_SPACE => "{$formattedAmount} {$this->symbol}",
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
