<?php

declare(strict_types=1);

namespace EsfahanAhan\Money\Enums;

enum CurrencyPositionEnum: string
{
    /**
     * Left.
     */
    case LEFT = 'left';

    /**
     * Left with space.
     */
    case LEFT_WITH_SPACE = 'left-with-space';

    /**
     * Right.
     */
    case RIGHT = 'right';

    /**
     * Right with space.
     */
    case RIGHT_WITH_SPACE = 'right-with-space';

    /**
     * @return array<value-of<self>,string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (CurrencyPositionEnum::cases() as $case) {
            $options[$case->value] = static::translate($case);
        }

        return $options;
    }

    private static function translate(self $enum): string
    {
        if (function_exists('trans')) {
            // @phpstan-ignore-next-line
            return (string) trans("esfahanahan::money.enums.currency-position.{$enum->value}");
        }

        return $enum->value;
    }
}
