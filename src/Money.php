<?php

declare(strict_types=1);

namespace EsfahanAhan\Money;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Brick\Math\BigRational;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Context\DefaultContext;
use Brick\Money\Currency as BrickCurrency;
use Brick\Money\Money as BrickMoney;
use EsfahanAhan\Money\Contracts\ICurrency;

class Money implements \JsonSerializable
{
    public static function of(
        BigNumber|int|float|string $amount,
        ICurrency $currency,
        ?Context $context = null,
        RoundingMode $roundingMode = RoundingMode::UNNECESSARY,
    ): Money {
        if (null === $context) {
            $context = new DefaultContext();
        }

        $amount = BigNumber::of($amount);

        return self::create($amount, $currency, $context, $roundingMode);
    }

    /**
     * Creates a Money from a rational amount, a currency, and a context.
     *
     * @param BigNumber    $amount       the amount
     * @param ICurrency    $currency     the currency
     * @param Context      $context      the context
     * @param RoundingMode $roundingMode an optional rounding mode if the amount does not fit the context
     *
     * @throws RoundingNecessaryException if RoundingMode::UNNECESSARY is used but rounding is necessary
     */
    public static function create(BigNumber $amount, ICurrency $currency, Context $context, RoundingMode $roundingMode = RoundingMode::UNNECESSARY): Money
    {
        $amount = $context->applyTo(
            $amount,
            new BrickCurrency($currency->getCode(), $currency->getId(), $currency->getName(), $currency->getDecimal()),
            $roundingMode
        );

        return new Money($amount, $currency, $context);
    }
    protected BigInteger|BigRational|BigDecimal $amount;

    public function __construct(
        BigNumber|int|float|string $amount,
        protected ICurrency $currency,
        protected Context $context,
    ) {
        $this->amount = BigNumber::of($amount); // @phpstan-ignore-line
    }

    /**
     * @return array{amount:string,currency:array{id:int,code:string,name:string,symbol:string}}
     */
    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    public function getAmount(): BigNumber
    {
        return $this->amount;
    }

    public function getCurrency(): ICurrency
    {
        return $this->currency;
    }

    public function format(): string
    {
        return $this->currency->format($this->amount);
    }

    /**
     * Adds another Money object of the same currency.
     */
    public function add(Money $other): Money
    {
        $this->assertSameCurrency($other);
        $newAmount = $this->amount->plus($other->amount);

        return new Money($newAmount, $this->currency, $this->context);
    }

    /**
     * Subtracts another Money object of the same currency.
     */
    public function subtract(Money $other): Money
    {
        $this->assertSameCurrency($other);
        $newAmount = $this->amount->minus($other->amount);

        return new Money($newAmount, $this->currency, $this->context);
    }

    /**
     * Multiplies the amount by a factor.
     */
    public function multiply(BigNumber|int|float|string $factor): Money
    {
        $newAmount = $this->amount->multipliedBy($factor);

        return new Money($newAmount, $this->currency, $this->context);
    }

    /**
     * Divides the amount by a divisor.
     */
    public function divide(BigNumber|int|float|string $divisor): Money
    {
        $newAmount = $this->amount->dividedBy($divisor);

        return new Money($newAmount, $this->currency, $this->context);
    }

    /**
     * Checks if this Money is equal to another Money (amount and currency).
     */
    public function equals(Money $other): bool
    {
        return $this->currency->getCode() === $other->currency->getCode()
            && $this->amount->isEqualTo($other->amount);
    }

    /**
     * Checks if this Money is greater than another Money (same currency).
     */
    public function greaterThan(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount->isGreaterThan($other->amount);
    }

    /**
     * Checks if this Money is less than another Money (same currency).
     */
    public function lessThan(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount->isLessThan($other->amount);
    }

    /**
     * Returns a new Money object with the absolute value of the amount.
     */
    public function abs(): Money
    {
        $newAmount = $this->amount->abs();

        return new Money($newAmount, $this->currency, $this->context);
    }

    /**
     * Returns true if the amount is zero.
     */
    public function isZero(): bool
    {
        return $this->amount->isZero();
    }

    /**
     * Returns true if the amount is positive.
     */
    public function isPositive(): bool
    {
        return $this->amount->isPositive();
    }

    /**
     * Returns true if the amount is negative.
     */
    public function isNegative(): bool
    {
        return $this->amount->isNegative();
    }

    /**
     * Returns a BrickMoney instance for interoperability.
     */
    public function toBrickMoney(): BrickMoney
    {
        return BrickMoney::of(
            $this->amount,
            new BrickCurrency($this->currency->getCode(), $this->currency->getId(), $this->currency->getName(), $this->currency->getDecimal())
        );
    }

    /**
     * Returns a rounded Money object according to currency decimals.
     *
     * @phpstan-param RoundingMode::* $mode
     */
    public function rounded(RoundingMode $mode = RoundingMode::HALF_UP): Money
    {
        $scale = $this->currency->getDecimal();
        $rounded = BigDecimal::of($this->amount)->toScale($scale, $mode);

        return new Money($rounded, $this->currency, $this->context);
    }

    /**
     * Returns the minimum of two Money objects (same currency).
     */
    public function min(Money $other): Money
    {
        $this->assertSameCurrency($other);

        return $this->amount->isLessThan($other->amount) ? $this : $other;
    }

    /**
     * Returns the maximum of two Money objects (same currency).
     */
    public function max(Money $other): Money
    {
        $this->assertSameCurrency($other);

        return $this->amount->isGreaterThan($other->amount) ? $this : $other;
    }

    /**
     * Returns a new Money object with the negated amount.
     */
    public function negated(): Money
    {
        $decAmount = BigDecimal::of($this->amount);

        return new Money($decAmount->negated(), $this->currency, $this->context);
    }

    /**
     * Converts this Money to another currency (stub, needs exchange rate).
     *
     * @param float|BigNumber $rate
     */
    public function convertTo(ICurrency $targetCurrency, $rate): Money
    {
        // You should inject a real exchange service for production use
        $decAmount = BigDecimal::of($this->amount);
        $newAmount = $decAmount->multipliedBy($rate);

        return new Money($newAmount, $targetCurrency, $this->context);
    }

    /**
     * Serializes the Money object to JSON.
     *
     * @return array{amount:string,currency:array{id:int,code:string,name:string,symbol:string}}
     */
    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount->jsonSerialize(),
            'currency' => [
                'id' => $this->currency->getId(),
                'code' => $this->currency->getCode(),
                'name' => $this->currency->getName(),
                'symbol' => $this->currency->getSymbol(),
            ],
        ];
    }

    /**
     * Helper to ensure two Money objects have the same currency.
     */
    protected function assertSameCurrency(Money $other): void
    {
        if (!$this->currency->isSameAs($other->getCurrency())) {
            throw new \InvalidArgumentException('Currency mismatch.');
        }
    }
}
