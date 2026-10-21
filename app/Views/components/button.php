<?php
/**
 * SecurePoll Button Component
 * @var string $label
 * @var string $variant (primary|secondary|outline|danger|ghost)
 * @var string|null $href
 * @var string $type (button|submit|reset)
 * @var string|null $icon
 * @var string $iconPosition (left|right)
 * @var string $size (sm|md|lg)
 * @var bool $disabled
 * @var string $class
 * @var string $attrs
 * @var string|null $id
 */

$variant = $variant ?? 'primary';
$label = $label ?? '';
$href = $href ?? null;
$type = $type ?? 'button';
$icon = $icon ?? null;
$iconPosition = $iconPosition ?? 'left';
$size = $size ?? 'md';
$disabled = !empty($disabled);
$class = $class ?? '';
$attrs = $attrs ?? '';
$id = $id ?? null;

$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 interactive-tap disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed';

$variantClasses = [
    'primary'   => 'bg-indigo-600 hover:bg-indigo-700 text-white border border-transparent shadow-xs',
    'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:border-slate-700 shadow-xs',
    'outline'   => 'bg-transparent hover:bg-indigo-50 text-indigo-600 border border-indigo-300 dark:hover:bg-indigo-950/30 dark:text-indigo-400 dark:border-indigo-800',
    'danger'    => 'bg-rose-600 hover:bg-rose-700 text-white border border-transparent shadow-xs',
    'ghost'     => 'bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/60 dark:hover:text-slate-200 border border-transparent',
];

$sizeClasses = [
    'sm' => 'text-xs px-2.5 py-1.5 gap-1.5',
    'md' => 'text-sm px-3.5 py-2 gap-2',
    'lg' => 'text-base px-5 py-2.5 gap-2.5',
];

$iconSizes = [
    'sm' => 'w-3.5 h-3.5',
    'md' => 'w-4 h-4',
    'lg' => 'w-5 h-5',
];

$chosenStyle = $variantClasses[$variant] ?? $variantClasses['primary'];
$chosenSize = $sizeClasses[$size] ?? $sizeClasses['md'];
$chosenIconSize = $iconSizes[$size] ?? $iconSizes['md'];
$idAttr = $id ? 'id="' . htmlspecialchars($id) . '"' : '';

$contentHtml = '';
if ($icon && $iconPosition === 'left') {
    $contentHtml .= '<i data-lucide="' . htmlspecialchars($icon) . '" class="' . $chosenIconSize . ' shrink-0"></i>';
}
if ($label !== '') {
    $contentHtml .= '<span>' . htmlspecialchars($label) . '</span>';
}
if ($icon && $iconPosition === 'right') {
    $contentHtml .= '<i data-lucide="' . htmlspecialchars($icon) . '" class="' . $chosenIconSize . ' shrink-0"></i>';
}

if ($href && !$disabled) {
    echo sprintf(
        '<a href="%s" %s class="%s %s %s %s" %s>%s</a>',
        htmlspecialchars($href),
        $idAttr,
        $baseClasses,
        $chosenStyle,
        $chosenSize,
        htmlspecialchars($class),
        $attrs,
        $contentHtml
    );
} else {
    $disabledAttr = $disabled ? 'disabled' : '';
    echo sprintf(
        '<button type="%s" %s class="%s %s %s %s" %s %s>%s</button>',
        htmlspecialchars($type),
        $idAttr,
        $baseClasses,
        $chosenStyle,
        $chosenSize,
        htmlspecialchars($class),
        $disabledAttr,
        $attrs,
        $contentHtml
    );
}
