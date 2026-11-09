<?php
// Biến: $title, $proposals, $page, $totalPages, $total
$title = $title ?? 'Đề xuất của tôi';
require APP_ROOT . '/app/views/partials/header.php';
?>
<div class="wl-page-head">
  <div>
    <h1>Đề xuất của tôi</h1>
    <p><?= (int) $total ?> đề xuất đã gửi</p>
  </div>
  <a class="btn btn-outline-primary" href="<?= e(url('project/index')) ?>">Tìm thêm dự án</a>
</div>

<?php if (empty($proposals)): ?>
  <?php
  $emptyTitle = 'Bạn chưa gửi đề xuất nào';
  $emptyText = 'Tìm một dự án phù hợp và gửi đề xuất đầu tiên.';
  $emptyActionUrl = url('project/index');
  $emptyActionLabel = 'Tìm dự án';
  require APP_ROOT . '/app/views/partials/empty_state.php';
  ?>
<?php else: ?>
  <ul class="wl-list">
    <?php foreach ($proposals as $d): ?>
      <?php $editable = $d['TrangThai'] === 'cho_duyet' && $d['TrangThaiDuAn'] === 'mo'; ?>
      <li class="wl-item" id="dx-<?= (int) $d['ID'] ?>">
        <div class="wl-item__main">
          <h2 class="wl-item__title"><a href="<?= e(url('project/show') . '?id=' . (int) $d['ID_DuAn']) ?>"><?= e($d['TieuDe']) ?></a></h2>
          <p class="wl-item__meta">
            <span>Chi phí <strong><?= e(wl_money($d['ChiPhiDeXuat'])) ?></strong></span>
            <span>Thời gian <strong><?= (int) $d['ThoiGianThucHien'] ?> ngày</strong></span>
            <span>Hạn nhận đề xuất <?= e(wl_date($d['HanNhanDeXuat'])) ?></span>
            <span>Gửi ngày <?= e(wl_date($d['NgayTao'])) ?></span>
          </p>
          <p class="wl-item__desc"><?= e($d['NoiDung']) ?></p>
          <?php if ($editable): ?>
            <div class="wl-actions">
              <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#wl-edit-proposal"
                      data-fill-id="<?= (int) $d['ID'] ?>" data-fill-chiphi="<?= (int) $d['ChiPhiDeXuat'] ?>"
                      data-fill-thoigian="<?= (int) $d['ThoiGianThucHien'] ?>" data-fill-noidung="<?= e($d['NoiDung']) ?>">Sửa</button>
              <form action="<?= e(url('proposal/withdraw')) ?>" method="post" data-ajax data-danger data-confirm="Rút đề xuất cho dự án &quot;<?= e($d['TieuDe']) ?>&quot;? Đề xuất sẽ bị xóa." data-confirm-ok="Rút đề xuất" novalidate>
                <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $d['ID'] ?>">
                <div class="form-alert d-none" role="alert"></div>
                <button type="submit" class="btn btn-danger-soft btn-sm">Rút đề xuất</button>
              </form>
            </div>
          <?php endif; ?>
        </div>
        <div class="wl-item__side">
          <?= wl_badge('proposal', $d['TrangThai']) ?>
          <span class="wl-muted small">Dự án: <?= wl_badge('project', $d['TrangThaiDuAn']) ?></span>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php
  $pagerUrl = url('proposal/mine');
  $pagerParams = [];
  require APP_ROOT . '/app/views/partials/pagination.php';
  ?>

  <div class="modal fade" id="wl-edit-proposal" tabindex="-1" aria-labelledby="wl-edit-proposal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" action="<?= e(url('proposal/update')) ?>" method="post" data-ajax novalidate>
        <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
        <input type="hidden" name="id" value="">
        <div class="modal-header">
          <h2 class="modal-title fs-5" id="wl-edit-proposal-title">Sửa đề xuất</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <div class="form-alert d-none" role="alert"></div>
          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label for="edit-chiphi" class="form-label">Chi phí đề xuất (VNĐ)</label>
              <input type="number" class="form-control" id="edit-chiphi" name="chiphi" min="1" step="1" inputmode="numeric" required data-money-preview="edit-chiphi-preview">
              <div class="wl-money-preview" id="edit-chiphi-preview" aria-live="polite"></div>
            </div>
            <div class="col-sm-6">
              <label for="edit-thoigian" class="form-label">Thời gian (ngày)</label>
              <input type="number" class="form-control" id="edit-thoigian" name="thoigian" min="1" max="365" step="1" inputmode="numeric" required>
            </div>
          </div>
          <div>
            <label for="edit-noidung" class="form-label">Nội dung đề xuất</label>
            <textarea class="form-control" id="edit-noidung" name="noidung" rows="5" minlength="20" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
