<?php
// Trạng thái rỗng dùng chung.
// Cần: $emptyTitle. Tùy chọn: $emptyText, $emptyActionUrl, $emptyActionLabel.
?>
<div class="wl-empty" role="status">
  <p class="wl-empty__title"><?= e($emptyTitle ?? 'Chưa có dữ liệu') ?></p>
  <?php if (!empty($emptyText)): ?>
    <p class="wl-empty__text"><?= e($emptyText) ?></p>
  <?php endif; ?>
  <?php if (!empty($emptyActionUrl) && !empty($emptyActionLabel)): ?>
    <a class="btn btn-primary" href="<?= e($emptyActionUrl) ?>"><?= e($emptyActionLabel) ?></a>
  <?php endif; ?>
</div>
