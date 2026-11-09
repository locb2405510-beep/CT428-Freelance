<?php
// Biến: $title, $project (ID, TieuDe, MoTa, NganSach, HanNhanDeXuat, KyNang, ...)
$title = $title ?? 'Sửa dự án';
require APP_ROOT . '/app/views/partials/header.php';
?>
<p class="mb-2"><a href="<?= e(url('project/show') . '?id=' . (int) $project['ID']) ?>">&larr; Quay lại dự án</a></p>
<div class="wl-page-head">
  <div>
    <h1>Sửa dự án</h1>
    <p>Chỉ sửa được khi dự án còn đang nhận đề xuất.</p>
  </div>
</div>

<div class="wl-form-medium">
  <div class="wl-card">
    <form action="<?= e(url('project/update')) ?>" method="post" data-ajax novalidate>
      <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
      <input type="hidden" name="id" value="<?= (int) $project['ID'] ?>">
      <div class="form-alert d-none" role="alert"></div>

      <div class="mb-3">
        <label for="tieude" class="form-label">Tiêu đề</label>
        <input type="text" class="form-control" id="tieude" name="tieude" minlength="5" maxlength="150" value="<?= e($project['TieuDe']) ?>" required>
      </div>
      <div class="mb-3">
        <label for="mota" class="form-label">Mô tả công việc</label>
        <textarea class="form-control" id="mota" name="mota" rows="6" minlength="20" required><?= e($project['MoTa']) ?></textarea>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-sm-6">
          <label for="ngansach" class="form-label">Ngân sách (VNĐ)</label>
          <input type="number" class="form-control" id="ngansach" name="ngansach" min="1" step="1" inputmode="numeric" value="<?= (int) $project['NganSach'] ?>" required data-money-preview="ngansach-preview">
          <div class="wl-money-preview" id="ngansach-preview" aria-live="polite"></div>
        </div>
        <div class="col-sm-6">
          <label for="han" class="form-label">Hạn nhận đề xuất</label>
          <input type="date" class="form-control" id="han" name="han" min="<?= e(date('Y-m-d')) ?>" value="<?= e($project['HanNhanDeXuat']) ?>" required>
        </div>
      </div>
      <div class="mb-4">
        <label for="kynang" class="form-label">Kỹ năng cần có <span class="wl-muted fw-normal">(không bắt buộc)</span></label>
        <input type="text" class="form-control" id="kynang" name="kynang" maxlength="500" value="<?= e($project['KyNang'] ?? '') ?>" placeholder="php, mysql, thiết kế web">
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a class="btn btn-outline-secondary" href="<?= e(url('project/show') . '?id=' . (int) $project['ID']) ?>">Hủy</a>
      </div>
    </form>
  </div>
</div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
