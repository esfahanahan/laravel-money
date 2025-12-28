<?php

declare(strict_types=1);

namespace EsfahanAhan\Money\Database\Factories;

use EsfahanAhan\Money\Enums\CurrencyPosition;
use EsfahanAhan\Money\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Currency>
     */
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array{
     *  code:non-empty-string,
     *  name: string,
     *  symbol: string,
     *  decimal: int,
     *  group_separator: non-empty-string,
     *  decimal_separator: non-empty-string,
     *  currency_position: value-of<CurrencyPosition>
     * }
     */
    public function definition(): array
    {
        return [
            'code' => $this->currencyCode(),
            'name' => $this->faker->word,
            'symbol' => $this->symbol(),
            'decimal' => $this->faker->numberBetween(0, 3),
            'group_separator' => ',',
            'decimal_separator' => '.',
            'currency_position' => CurrencyPosition::LEFT->value,
        ];
    }

    protected function symbol(): string
    {
        $symbols = ['$', '€', '£', '¥', '₹', '₩', '₽', '₺', '₪', '₫', '₴', '₦', '₱', '฿', '₡', '₲', '₵', '₸', '₭', '₮', '₤', '₳', '₥', '₯', '₰', '₠', '₣', '₧'];
        $key = array_rand($symbols);

        return $symbols[$key];
    }

    /**
     * @return non-empty-string
     */
    protected function currencyCode(): string
    {
        return $this->faker->unique()->currencyCode ?: 'IRR';
    }
}
