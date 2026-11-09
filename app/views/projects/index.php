<?php
// Trang tìm dự án cho freelancer. Dữ liệu lấy bằng AJAX từ project/search (JSON).
$title = $title ?? 'Tìm dự án';
require APP_ROOT . '/app/views/partials/header.php';
?>
<div class="wl-page-head">
  <div>
    <h1>Tìm dự án</h1>
    <p>Chỉ hiện các dự án đang nhận đề xuất và còn hạn.</p>
  </div>
</div>

<form id="wl-search" class="wl-card wl-search" action="<?= e(url('project/index')) ?>" method="get"
      data-endpoint="<?= e(url('project/search')) ?>" data-detail="<?= e(url('project/show')) ?>" role="search">
  <div class="c-q">
    <label for="q" class="form-label">Từ khóa</label>
    <input type="search" class="form-control" id="q" name="q" placeholder="Ví dụ: website, logo, API" autocomplete="off">
  </div>
  <div class="c-min">
    <label for="min" class="form-label">Ngân sách từ</label>
    <input type="number" class="form-control" id="min" name="min" min="0" step="1" inputmode="numeric" placeholder="VNĐ">
  </div>
  <div class="c-max">
    <label for="max" class="form-label">đến</label>
    <input type="number" class="form-control" id="max" name="max" min="0" step="1" inputmode="numeric" placeholder="VNĐ">
  </div>
  <div class="c-kn">
    <label for="kynang" class="form-label">Kỹ năng</label>
    <input type="text" class="form-control" id="kynang" name="kynang" placeholder="php, figma..." autocomplete="off">
  </div>
  <div class="c-sort">
    <label for="sort" class="form-label">Sắp xếp</label>
    <select class="form-select" id="sort" name="sort">
      <option value="newest">Mới đăng nhất</option>
      <option value="budget_desc">Ngân sách cao đến thấp</option>
      <option value="budget_asc">Ngân sách thấp đến cao</option>
      <option value="deadline">Sắp hết hạn trước</option>
    </select>
  </div>
  <div class="c-btn">
    <button type="submit" class="btn btn-primary flex-fill">Tìm</button>
    <button type="button" class="btn btn-outline-secondary" id="wl-search-reset">Xóa lọc</button>
  </div>
</form>

<p class="wl-result-info" id="wl-result-info" role="status" aria-live="polite"></p>
<div id="wl-results" aria-live="polite">
  <noscript><div class="alert alert-warning">Trang này cần bật JavaScript để hiện danh sách dự án.</div></noscript>
</div>
<div id="wl-search-pager"></div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
