<?php

namespace App\Enums;

/**
 * Layouts available under resources/views/designs/categories/.
 * Add a new case here whenever you add a new Blade file to that folder.
 */
enum CategoryLayoutType: string
{
     case Default   = 'default';    // designs/categories/default.blade.php
     case Minimal   = 'minimal';    // designs/categories/minimal.blade.php
     case Editorial = 'editorial';  // designs/categories/editorial.blade.php
     case Card      = 'card';       // designs/categories/card.blade.php

    public function label(): string
    {
        return match ($this) {
            self::Default   => 'Default - Dark gradient hero with card grid',
            self::Minimal   => 'Minimal - Clean stacked list, no hero',
            self::Editorial => 'Editorial - Two-column category layout',
            self::Card      => 'Card - Grid cards with compact summaries',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return collect(self::cases())
            ->map(fn (self $case): string => $case->value)
            ->values()
            ->all();
    }
}
