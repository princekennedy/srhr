<?php

namespace App\Enums;

/**
 * Layouts available under resources/views/designs/navigation/.
 * Add a new case here whenever you add a new Blade file to that folder.
 */
enum MenuItemLayoutType: string
{
     case Default   = 'default';    // designs/navigation/default.blade.php
     case Minimal   = 'minimal';    // designs/navigation/minimal.blade.php
     case Editorial = 'editorial';  // designs/navigation/editorial.blade.php
     case Card      = 'card';       // designs/navigation/card.blade.php

    public function label(): string
    {
        return match ($this) {
            self::Default   => 'Default - Dark gradient hero with sectioned content cards',
            self::Minimal   => 'Minimal - Clean stacked list of linked content',
            self::Editorial => 'Editorial - Two-column article-first page layout',
            self::Card      => 'Card - Grid cards with compact content summaries',
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
