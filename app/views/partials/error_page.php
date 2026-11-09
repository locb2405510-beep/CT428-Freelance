<?php
// Trang lỗi dùng chung cho 403 và 404.
// Cần: $errorCode (số), $errorHeading, $errorText.
$title = $title ?? ($errorHeading ?? 'Lỗi');
require APP_ROOT . '/app/views/partials/header.php';
?>
<div class="wl-error">
  <p class="wl-error__code" aria-hidden="true"><?= (int) $errorCode ?></p>
  <h1 class="fs-3 fw-bold"><?= e($errorHeading) ?></h1>
  <p class="wl-muted mb-4"><?= e($errorText) ?></p>
  <div class="d-flex flex-wrap justify-content-center gap-2">
    <a class="btn btn-primary" href="<?= e(url('')) ?>">Về trang chủ</a>
    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Quay lại</button>
  </div>
</div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
