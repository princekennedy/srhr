<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicSiteConfig
{
    public function data(): array
    {
        $settings = $this->settings();

        $appName = $this->stringValue($settings['app_name'] ?? null) ?: config('app.name', 'SRHR Connect');
        $welcomeMessage = $this->stringValue($settings['welcome_message'] ?? null)
            ?: 'Trusted SRHR guidance and support access in one place.';
        $supportPhone = $this->stringValue($settings['support_phone'] ?? null);
        $supportEmail = $this->stringValue($settings['support_email'] ?? null);
        $defaultMode = in_array($settings['theme_mode'] ?? null, ['light', 'dark'], true)
            ? $settings['theme_mode']
            : 'light';
        $theme = $this->themePalette($this->normalizeHexColor($settings['theme_accent'] ?? null, '#009bde'));

        return [
            'brand' => [
                'name' => $appName,
                'kicker' => 'SRHR Platform',
                'message' => $welcomeMessage,
                'strapline' => Str::limit($welcomeMessage, 72),
            ],
            'homepage' => [
                'slides' => collect(range(1, 3))
                    ->map(fn (int $index): array => [
                        'image_url' => $this->stringValue($settings['hero_slide_'.$index.'_image'] ?? null),
                    ])
                    ->all(),
            ],
            'support' => [
                'phone' => $supportPhone,
                'phone_href' => filled($supportPhone) ? 'tel:'.preg_replace('/\s+/', '', $supportPhone) : null,
                'email' => $supportEmail,
                'email_href' => filled($supportEmail) ? 'mailto:'.$supportEmail : null,
            ],
            'theme' => [
                ...$theme,
                'default_mode' => $defaultMode,
            ],
            'features' => [
                'registration_required' => (bool) ($settings['onboarding_requires_registration'] ?? false),
            ],
        ];
    }

    private function settings(): array
    {
        if (! Schema::hasTable('app_settings')) {
            return [];
        }

        return AppSetting::query()
            ->where('is_public', true)
            ->get()
            ->mapWithKeys(fn (AppSetting $setting): array => [$setting->key => $this->settingValue($setting)])
            ->all();
    }

    private function settingValue(AppSetting $setting): mixed
    {
        if ($setting->value === null) {
            return null;
        }

        return match ($setting->input_type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($setting->value) ? $setting->value + 0 : $setting->value,
            'json' => json_decode($setting->value, true) ?? $setting->value,
            default => $setting->value,
        };
    }

    private function stringValue(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }

    private function normalizeHexColor(mixed $value, string $fallback): string
    {
        if (! is_string($value)) {
            return $fallback;
        }

        $candidate = ltrim(trim($value), '#');

        if (preg_match('/^[0-9a-fA-F]{3}$/', $candidate) === 1) {
            return '#'.Str::of($candidate)
                ->split('/(?=.)/')
                ->map(fn (string $character): string => $character.$character)
                ->implode('');
        }

        if (preg_match('/^[0-9a-fA-F]{6}$/', $candidate) === 1) {
            return '#'.Str::lower($candidate);
        }

        return $fallback;
    }

    private function themePalette(string $accent): array
    {
        $accentRgb = $this->hexToRgb($accent);
        $accentStrong = $this->darken($accentRgb, 0.18);
        $warm = $this->darken($accentRgb, 0.35);

        return [
            'accent' => $accent,
            'accent_strong' => $this->rgbToHex($accentStrong),
            'accent_soft' => $this->rgba($accentRgb, 0.12),
            'warm' => $this->rgbToHex($warm),
            'warm_soft' => $this->rgba($warm, 0.12),
        ];
    }

    private function hexToRgb(string $hex): array
    {
        $normalized = ltrim($hex, '#');

        return [
            hexdec(substr($normalized, 0, 2)),
            hexdec(substr($normalized, 2, 2)),
            hexdec(substr($normalized, 4, 2)),
        ];
    }

    private function darken(array $rgb, float $ratio): array
    {
        return array_map(
            static fn (int $channel): int => max(0, min(255, (int) round($channel * (1 - $ratio)))),
            $rgb,
        );
    }

    private function rgbToHex(array $rgb): string
    {
        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }

    private function rgba(array $rgb, float $alpha): string
    {
        return sprintf('rgba(%d, %d, %d, %.2f)', $rgb[0], $rgb[1], $rgb[2], $alpha);
    }
}