<?php
/**
 * SecurePoll Badge Component
 * @var string $label
 * @var string $variant (active|closed|scheduled|draft|public|private|anonymous|info|warning|danger|neutral)
 * @var string|null $icon
 * @var string $size (sm|md)
 * @var string $class
 */

$variant = $variant ?? ($type ?? 'neutral');
$size = $size ?? 'sm';
$class = $class ?? '';
$label = $label ?? '';
$icon = $icon ?? null;

$variantClasses = [
    'active'    => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50',
    'success'   => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/50',
    'closed'    => 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/50',
    'danger'    => 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/50',
    'scheduled' => 'bg-sky-50 text-sky-700 border-sky-200/80 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800/50',
    'info'      => 'bg-sky-50 text-sky-700 border-sky-200/80 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800/50',
    'draft'     => 'bg-amber-50 text-amber-800 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50',
    'warning'   => 'bg-amber-50 text-amber-800 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/50',
    'public'    => 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800/50',
    'private'   => 'bg-purple-50 text-purple-700 border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800/50',
    'anonymous' => 'bg-teal-50 text-teal-700 border-teal-200/80 dark:bg-teal-950/40 dark:text-teal-400 dark:border-teal-800/50',
    'neutral'   => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
];

$chosenStyle = $variantClasses[strtolower($variant)] ?? $variantClasses['neutral'];

$sizeClasses = [
    'sm' => 'text-[11px] px-2 py-0.5 gap-1',
    'md' => 'text-xs px-2.5 py-1 gap-1.5',
];

$chosenSize = $sizeClasses[$size] ?? $sizeClasses['sm'];
$iconSize = $size === 'sm' ? 'w-3 h-3' : 'w-3.5 h-3.5';
?>
<span class="inline-flex items-center font-medium border rounded-full shrink-0 tracking-wide <?= $chosenStyle ?> <?= $chosenSize ?> <?= htmlspecialchars($class) ?>">
    <?php if ($icon): ?>
        <i data-lucide="<?= htmlspecialchars($icon) ?>" class="<?= $iconSize ?> shrink-0"></i>
    <?php endif; ?>
    <span><?= htmlspecialchars($label) ?></span>
</span>
