<?php
require_once __DIR__ . '/config.php';

/**
 * Get global theme from settings table.
 * Returns ['mode' => 'dark'|'light', 'accent' => 'blue'|'green'|'purple']
 */
function rp_get_theme(): array
{
    $mode = rp_get_setting('theme_mode', 'dark');
    $accent = rp_get_setting('theme_accent', 'blue');

    if (!in_array($mode, ['dark', 'light'], true)) {
        $mode = 'dark';
    }

    if (!in_array($accent, ['blue', 'green', 'purple'], true)) {
        $accent = 'blue';
    }

    return [
        'mode' => $mode,
        'accent' => $accent,
    ];
}

/**
 * Combine global theme with optional user-specific accent.
 */
function rp_get_user_theme(?array $user): array
{
    $theme = rp_get_theme();
    if ($user && !empty($user['accent']) && in_array($user['accent'], ['blue', 'green', 'purple'], true)) {
        $theme['accent'] = $user['accent'];
    }
    return $theme;
}

/**
 * Build a small CSS snippet overriding core colors based on theme.
 * This CSS is meant to be injected after the main styles.
 */
function rp_theme_css(array $theme): string
{
    $mode = $theme['mode'] ?? 'dark';
    $accent = $theme['accent'] ?? 'blue';

    // Default (blue) palette
    $primaryStart = '#2b2eec';
    $primaryEnd = '#00b5ff';
    $accentColor = '#f35bff';
    $textMain = '#ffffff';
    $textSub = '#e4f2ff';
    $cardBg = 'rgba(7, 13, 62, 0.9)';

    if ($accent === 'green') {
        $primaryStart = '#0f766e';
        $primaryEnd = '#22c55e';
        $accentColor = '#a7f3d0';
    } elseif ($accent === 'purple') {
        $primaryStart = '#4c1d95';
        $primaryEnd = '#7c3aed';
        $accentColor = '#c4b5fd';
    }

    if ($mode === 'light') {
        $textMain = '#0f172a';
        $textSub = '#4b5563';
        $cardBg = 'rgba(255,255,255,0.98)';
    }

    return <<<CSS
:root {
    --primary-start: {$primaryStart};
    --primary-end: {$primaryEnd};
    --accent: {$accentColor};
    --text-main: {$textMain};
    --text-sub: {$textSub};
}
.theme-card-bg {
    background: {$cardBg};
}
CSS;
}