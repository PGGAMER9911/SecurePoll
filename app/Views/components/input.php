<?php
/**
 * SecurePoll Input Component
 * @var string $name
 * @var string|null $id
 * @var string|null $label
 * @var string $type
 * @var string $value
 * @var string|null $placeholder
 * @var bool $required
 * @var bool $disabled
 * @var string|null $error
 * @var string|null $hint
 * @var string|null $icon
 * @var string $class
 * @var string $attrs
 */

$name = $name ?? '';
$id = $id ?? $name;
$label = $label ?? null;
$type = $type ?? 'text';
$value = $value ?? '';
$placeholder = $placeholder ?? '';
$required = !empty($required);
$disabled = !empty($disabled);
$error = $error ?? null;
$hint = $hint ?? null;
$icon = $icon ?? null;
$class = $class ?? '';
$attrs = $attrs ?? '';

$baseInputClasses = 'w-full text-sm rounded-lg border bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 disabled:bg-slate-50 disabled:dark:bg-slate-800 disabled:cursor-not-allowed';

$borderClasses = $error 
    ? 'border-rose-400 dark:border-rose-500 focus:border-rose-500 focus:ring-rose-500/20' 
    : 'border-slate-300 dark:border-slate-700';

$paddingClasses = $icon ? 'pl-9 pr-3.5 py-2' : 'px-3.5 py-2';
?>
<div class="space-y-1.5 <?= htmlspecialchars($class) ?>">
    <?php if ($label): ?>
        <label for="<?= htmlspecialchars($id) ?>" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">
            <?= htmlspecialchars($label) ?>
            <?php if ($required): ?>
                <span class="text-rose-500">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    
    <div class="relative">
        <?php if ($icon): ?>
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                <i data-lucide="<?= htmlspecialchars($icon) ?>" class="w-4 h-4"></i>
            </div>
        <?php endif; ?>
        
        <input 
            type="<?= htmlspecialchars($type) ?>" 
            name="<?= htmlspecialchars($name) ?>" 
            id="<?= htmlspecialchars($id) ?>" 
            value="<?= htmlspecialchars($value) ?>" 
            placeholder="<?= htmlspecialchars($placeholder) ?>" 
            <?= $required ? 'required' : '' ?> 
            <?= $disabled ? 'disabled' : '' ?> 
            class="<?= $baseInputClasses ?> <?= $borderClasses ?> <?= $paddingClasses ?>"
            <?= $attrs ?>
        >
    </div>
    
    <?php if ($error): ?>
        <p class="text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1 mt-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </p>
    <?php elseif ($hint): ?>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1"><?= htmlspecialchars($hint) ?></p>
    <?php endif; ?>
</div>
