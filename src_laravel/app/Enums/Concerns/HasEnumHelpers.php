<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * @method static array cases()
 */
trait HasEnumHelpers
{
    public static function translationKey(): string
    {
        return Str::of(class_basename(static::class))
            ->replaceLast('Enum', '')
            ->snake()
            ->toString();
    }

    public function label(): string
    {
        $key = 'enums.' . static::translationKey() . '.' . $this->value;

        return Lang::has($key) ? __($key) : $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->all();
    }
}