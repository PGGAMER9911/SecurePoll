<?php
/**
 * SecurePoll Modal Component
 * @var string $id
 * @var string $title
 * @var string|null $subtitle
 * @var string $body
 * @var string $confirmLabel
 * @var string|null $confirmAction
 * @var string $confirmVariant (primary|danger)
 * @var string $cancelLabel
 * @var string|null $icon
 */

$id = $id ?? 'modal';
$title = $title ?? 'Modal Title';
$subtitle = $subtitle ?? null;
$body = $body ?? '';
$confirmLabel = $confirmLabel ?? 'Confirm';
$confirmAction = $confirmAction ?? '';
$confirmVariant = $confirmVariant ?? 'primary';
$cancelLabel = $cancelLabel ?? 'Cancel';
$icon = $icon ?? null;

$confirmBtnClass = $confirmVariant === 'danger'
    ? 'bg-rose-600 hover:bg-rose-700 text-white'
    : 'bg-indigo-600 hover:bg-indigo-700 text-white';
?>
<div id="<?= htmlspecialchars($id) ?>" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="<?= htmlspecialchars($id) ?>-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity" onclick="closeModal('<?= htmlspecialchars($id) ?>')"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <!-- Header -->
            <div class="px-6 pt-6 pb-4 flex items-start gap-4">
                <?php if ($icon): ?>
                    <div class="w-10 h-10 rounded-full <?= $confirmVariant === 'danger' ? 'bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400' : 'bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400' ?> flex items-center justify-center shrink-0">
                        <i data-lucide="<?= htmlspecialchars($icon) ?>" class="w-5 h-5"></i>
                    </div>
                <?php endif; ?>
                
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white" id="<?= htmlspecialchars($id) ?>-title">
                        <?= htmlspecialchars($title) ?>
                    </h3>
                    <?php if ($subtitle): ?>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            <?= htmlspecialchars($subtitle) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <button type="button" onclick="closeModal('<?= htmlspecialchars($id) ?>')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-3 text-sm text-slate-600 dark:text-slate-300">
                <?= $body ?>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 dark:bg-slate-900/80 px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                <button type="button" onclick="closeModal('<?= htmlspecialchars($id) ?>')" class="w-full sm:w-auto px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <?= htmlspecialchars($cancelLabel) ?>
                </button>
                <?php if ($confirmAction): ?>
                    <button type="button" <?= $confirmAction ?> class="w-full sm:w-auto px-4 py-2 text-sm font-medium rounded-lg <?= $confirmBtnClass ?> shadow-xs transition-colors">
                        <?= htmlspecialchars($confirmLabel) ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof window.openModal === 'undefined') {
    window.openModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            if (window.lucide) window.lucide.createIcons();
        }
    };
    window.closeModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    };
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(m => m.classList.add('hidden'));
            document.body.classList.remove('overflow-hidden');
        }
    });
}
</script>
