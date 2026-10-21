<?php
/**
 * SecurePoll Empty State Component
 * @var string|null $icon
 * @var string $title
 * @var string $description
 * @var string|null $actionLabel
 * @var string|null $actionHref
 * @var string|null $actionIcon
 * @var string|null $actionAttrs
 * @var string $class
 */

$icon = $icon ?? 'inbox';
$title = $title ?? 'No items found';
$description = $description ?? 'Get started by creating your first entry.';
$actionLabel = $actionLabel ?? null;
$actionHref = $actionHref ?? null;
$actionIcon = $actionIcon ?? 'plus';
$actionAttrs = $actionAttrs ?? '';
$class = $class ?? '';
?>
<div class="flex flex-col items-center justify-center p-8 sm:p-12 text-center rounded-xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/60 dark:bg-slate-900/40 <?= htmlspecialchars($class) ?>">
    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-3.5 border border-slate-200/60 dark:border-slate-700/60">
        <i data-lucide="<?= htmlspecialchars($icon) ?>" class="w-6 h-6"></i>
    </div>
    
    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1">
        <?= htmlspecialchars($title) ?>
    </h3>
    
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mb-5 leading-relaxed">
        <?= htmlspecialchars($description) ?>
    </p>

    <?php if ($actionLabel && $actionHref): ?>
        <a href="<?= htmlspecialchars($actionHref) ?>" <?= $actionAttrs ?> class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-colors interactive-tap">
            <?php if ($actionIcon): ?>
                <i data-lucide="<?= htmlspecialchars($actionIcon) ?>" class="w-4 h-4"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($actionLabel) ?></span>
        </a>
    <?php endif; ?>
</div>
