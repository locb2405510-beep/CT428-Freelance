<?php
// Biến: $title, $project, $isOwner, $proposals, $myProposal, $canPropose
$title = $title ?? $project['TieuDe'];
$proposals = $proposals ?? [];
$status = $project['TrangThai'];
$expired = $status === 'mo' && $project['HanNhanDeXuat'] < date('Y-m-d');
require APP_ROOT . '/app/views/partials/header.php';
$showUrl = url('project/show') . '?id=' . (int) $project['ID'];

/** Form nút ghi dữ liệu có hộp thoại xác nhận (quy ước mục 4.5). */
$actionForm = function (string $route, int $id, string $label, string $confirm, string $btnClass = 'btn-outline-secondary', bool $danger = false): string {
    return '<form action="' . e(url($route)) . '" method="post" data-ajax data-confirm="' . e($confirm) . '"' . ($danger ? ' data-danger' : '') . ' novalidate>'
        . '<input type="hidden" name="csrf" value="' . e(Csrf::token()) . '">'
        . '<input type="hidden" name="id" value="' . $id . '">'
        . '<div class="form-alert d-none" role="alert"></div>'
        . '<button type="submit" class="btn ' . $btnClass . ' w-100">' . e($label) . '</button></form>';
};
?>
<p class="mb-2"><a href="<?= e($isOwner ? url('project/mine') : url('project/index')) ?>">&larr; <?= $isOwner ? 'Dự án của tôi' : 'Tìm dự án' ?></a></p>

<div class="wl-page-head">
  <div>
    <h1><?= e($project['TieuDe']) ?></h1>
    <p>Đăng bởi <?= e($project['TenKhachHang']) ?> · <?= e(wl_date($project['NgayTao'])) ?></p>
  </div>
  <div><?= wl_badge('project', $status) ?><?php if ($expired): ?> <span class="wl-badge wl-badge--da_huy">Hết hạn nhận đề xuất</span><?php endif; ?></div>
</div>

<div class="wl-detail">
  <div>
    <section class="wl-card" aria-labelledby="mo-ta">
      <h2 class="wl-card__title" id="mo-ta">Mô tả công việc</h2>
      <p class="wl-desc mb-0"><?= e($project['MoTa']) ?></p>
    </section>

    <?php if ($isOwner): ?>
    <section class="wl-card" aria-labelledby="de-xuat">
      <h2 class="wl-card__title" id="de-xuat">Đề xuất nhận được (<?= count($proposals) ?>)</h2>
      <?php if (!$proposals): ?>
        <?php
        $emptyTitle = 'Chưa có đề xuất nào';
        $emptyText = $status === 'mo' ? 'Freelancer sẽ gửi đề xuất cho dự án này trước hạn ' . wl_date($project['HanNhanDeXuat']) . '.' : 'Dự án này không nhận thêm đề xuất.';
        $emptyActionUrl = $emptyActionLabel = null;
        require APP_ROOT . '/app/views/partials/empty_state.php';
        ?>
      <?php else: ?>
        <?php foreach ($proposals as $d): ?>
          <article class="wl-proposal<?= $d['TrangThai'] === 'duoc_chon' ? ' wl-proposal--chosen' : '' ?>">
            <div class="wl-proposal__head">
              <div>
                <span class="wl-proposal__name"><?= e($d['TenFreelancer']) ?></span>
                <?php if (array_key_exists('SoLuotDanhGia', $d) && (int) $d['SoLuotDanhGia'] > 0): ?>
                  <span class="wl-rating-text">★ <?= e(wl_stars($d['DiemTB'] ?? null, $d['SoLuotDanhGia'])) ?></span>
                <?php endif; ?>
              </div>
              <?= wl_badge('proposal', $d['TrangThai']) ?>
            </div>
            <p class="wl-proposal__meta">
              <span>Chi phí <strong><?= e(wl_money($d['ChiPhiDeXuat'])) ?></strong></span>
              <span>Thời gian <strong><?= (int) $d['ThoiGianThucHien'] ?> ngày</strong></span>
              <span>Gửi ngày <?= e(wl_date($d['NgayTao'])) ?></span>
            </p>
            <?php $sk = wl_skills($d['KyNangFreelancer'] ?? ''); if ($sk): ?>
              <ul class="wl-chips mb-2" aria-label="Kỹ năng của freelancer"><?php foreach ($sk as $s): ?><li class="wl-chip"><?= e($s) ?></li><?php endforeach; ?></ul>
            <?php endif; ?>
            <p class="wl-proposal__body"><?= e($d['NoiDung']) ?></p>
            <?php if ($status === 'mo' && $d['TrangThai'] === 'cho_duyet'): ?>
              <div style="max-width: 220px">
                <?= $actionForm('proposal/select', (int) $d['ID'], 'Chọn đề xuất này', 'Chọn đề xuất của ' . $d['TenFreelancer'] . '? Các đề xuất còn lại sẽ bị từ chối và dự án chuyển sang đang thực hiện.', 'btn-primary') ?>
              </div>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
    <?php endif; ?>
  </div>

  <aside>
    <section class="wl-card" aria-labelledby="thong-tin">
      <h2 class="wl-card__title" id="thong-tin">Thông tin</h2>
      <dl class="wl-facts">
        <div><dt>Ngân sách</dt><dd><?= e(wl_money($project['NganSach'])) ?></dd></div>
        <div><dt>Hạn nhận đề xuất</dt><dd><?= e(wl_date($project['HanNhanDeXuat'])) ?></dd></div>
        <?php $skills = wl_skills($project['KyNang'] ?? ''); if ($skills): ?>
          <div><dt>Kỹ năng</dt><dd><ul class="wl-chips"><?php foreach ($skills as $s): ?><li class="wl-chip"><?= e($s) ?></li><?php endforeach; ?></ul></dd></div>
        <?php endif; ?>
      </dl>
    </section>

    <?php if ($isOwner): ?>
    <section class="wl-card" aria-labelledby="thao-tac">
      <h2 class="wl-card__title" id="thao-tac">Thao tác</h2>
      <div class="d-grid gap-2">
        <?php if ($status === 'mo'): ?>
          <a class="btn btn-outline-primary" href="<?= e(url('project/edit') . '?id=' . (int) $project['ID']) ?>">Sửa dự án</a>
        <?php endif; ?>
        <?php if ($status === 'dang_thuc_hien'): ?>
          <?= $actionForm('project/complete', (int) $project['ID'], 'Đánh dấu hoàn thành', 'Xác nhận dự án đã hoàn thành? Sau đó không thể thay đổi trạng thái.', 'btn-primary') ?>
        <?php endif; ?>
        <?php if ($status === 'mo' || $status === 'dang_thuc_hien'): ?>
          <?= $actionForm('project/cancel', (int) $project['ID'], 'Hủy dự án', 'Hủy dự án này? Sau khi hủy không thể mở lại.', 'btn-danger-soft', true) ?>
        <?php endif; ?>
        <?php if ($status === 'mo' || $status === 'da_huy'): ?>
          <?= $actionForm('project/delete', (int) $project['ID'], 'Xóa dự án', 'Xóa dự án này và toàn bộ đề xuất của nó? Không thể khôi phục.', 'btn-danger-soft', true) ?>
        <?php endif; ?>
        <?php if (in_array($status, ['hoan_thanh', 'da_huy'], true)): ?>
          <p class="wl-muted small mb-0">Dự án đã kết thúc nên không đổi được trạng thái.</p>
        <?php endif; ?>
      </div>
    </section>
    <?php endif; ?>
  </aside>
</div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
