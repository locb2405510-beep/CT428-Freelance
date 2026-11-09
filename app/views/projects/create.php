<?php
$title = $title ?? 'Đăng dự án';
require APP_ROOT . '/app/views/partials/header.php';
?>
<div class="wl-page-head">
  <div>
    <h1>Đăng dự án mới</h1>
    <p>Mô tả rõ công việc để freelancer gửi đề xuất phù hợp.</p>
  </div>
</div>

<div class="wl-form-medium">
  <div class="wl-card">
    <form action="<?= e(url('project/store')) ?>" method="post" data-ajax novalidate>
      <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
      <div class="form-alert d-none" role="alert"></div>

      <div class="mb-3">
        <label for="tieude" class="form-label">Tiêu đề</label>
        <input type="text" class="form-control" id="tieude" name="tieude" minlength="5" maxlength="150" required aria-describedby="tieude-help">
        <div id="tieude-help" class="form-text">Từ 5 đến 150 ký tự.</div>
      </div>
      <div class="mb-3">
        <label for="mota" class="form-label">Mô tả công việc</label>
        <textarea class="form-control" id="mota" name="mota" rows="6" minlength="20" required aria-describedby="mota-help"></textarea>
        <div id="mota-help" class="form-text">Từ 20 ký tự. Nêu yêu cầu, sản phẩm bàn giao và các mốc quan trọng.</div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-sm-6">
          <label for="ngansach" class="form-label">Ngân sách (VNĐ)</label>
          <input type="number" class="form-control" id="ngansach" name="ngansach" min="1" step="1" inputmode="numeric" required data-money-preview="ngansach-preview">
          <div class="wl-money-preview" id="ngansach-preview" aria-live="polite"></div>
        </div>
        <div class="col-sm-6">
          <label for="han" class="form-label">Hạn nhận đề xuất</label>
          <input type="date" class="form-control" id="han" name="han" min="<?= e(date('Y-m-d')) ?>" required>
        </div>
      </div>
      <div class="mb-4">
        <label for="kynang" class="form-label">Kỹ năng cần có <span class="wl-muted fw-normal">(không bắt buộc)</span></label>
        <input type="text" class="form-control" id="kynang" name="kynang" maxlength="500" placeholder="php, mysql, thiết kế web" aria-describedby="kynang-help">
        <div id="kynang-help" class="form-text">Cách nhau bằng dấu phẩy.</div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">Đăng dự án</button>
        <a class="btn btn-outline-secondary" href="<?= e(url('project/mine')) ?>">Hủy</a>
      </div>
    </form>
  </div>
</div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
