<?php
/**
 * SecurePoll Card Component
 * @var string|null $title
 * @var string|null $subtitle
 * @var string|null $badge
 * @var string|null $actions
 * @var string|null $content
 * @var string|null $footer
 * @var string $class
 * @var string $headerClass
 * @var string $bodyClass
 */

$title = $title ?? null;
$subtitle = $subtitle ?? null;
$badge = $badge ?? null;
$actions = $actions ?? null;
$content = $content ?? '';
$footer = $footer ?? null;
$class = $class ?? '';
$headerClass = $headerClass ?? '';
$bodyClass = $bodyClass ?? '';
?>
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all <?= htmlspecialchars($class) ?>">
    <?php if ($title || $actions || $badge): ?>
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4 <?= htmlspecialchars($headerClass) ?>">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2.5">
                    <?php if ($title): ?>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($title) ?></h3>
                    <?php endif; ?>
                    <?php if ($badge): ?>
                        <div><?= $badge ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($subtitle): ?>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate"><?= htmlspecialchars($subtitle) ?></p>
                <?php endif; ?>
            </div>
            <?php if ($actions): ?>
                <div class="flex items-center gap-2 shrink-0">
                    <?= $actions ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="p-5 <?= htmlspecialchars($bodyClass) ?>">
        <?= $content ?>
    </div>
    
    <?php if ($footer): ?>
        <div class="px-5 py-3 bg-slate-50/75 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>
