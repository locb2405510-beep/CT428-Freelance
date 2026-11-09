<?php
// Biến: $title, $projects, $page, $totalPages, $total, $trangThai
$title = $title ?? 'Dự án của tôi';
$trangThai = $trangThai ?? '';
require APP_ROOT . '/app/views/partials/header.php';

$filters = ['' => 'Tất cả'] + wl_status_map('project');
$mineUrl = url('project/mine');
?>
<div class="wl-page-head">
  <div>
    <h1>Dự án của tôi</h1>
    <p><?= (int) $total ?> dự án<?= $trangThai !== '' ? ' ở trạng thái "' . e(wl_status_map('project')[$trangThai] ?? $trangThai) . '"' : '' ?></p>
  </div>
  <a class="btn btn-primary" href="<?= e(url('project/create')) ?>">Đăng dự án mới</a>
</div>

<nav class="wl-filter" aria-label="Lọc theo trạng thái">
  <?php foreach ($filters as $code => $label): ?>
    <a href="<?= e($code === '' ? $mineUrl : $mineUrl . '?' . http_build_query(['trangthai' => $code])) ?>"
       class="<?= $trangThai === (string) $code ? 'active' : '' ?>"<?= $trangThai === (string) $code ? ' aria-current="true"' : '' ?>><?= e($label) ?></a>
  <?php endforeach; ?>
</nav>

<?php if (empty($projects)): ?>
  <?php
  $emptyTitle = $trangThai === '' ? 'Bạn chưa đăng dự án nào' : 'Không có dự án ở trạng thái này';
  $emptyText = $trangThai === '' ? 'Đăng dự án đầu tiên để nhận đề xuất từ freelancer.' : 'Chọn trạng thái khác hoặc xem tất cả dự án.';
  $emptyActionUrl = $trangThai === '' ? url('project/create') : $mineUrl;
  $emptyActionLabel = $trangThai === '' ? 'Đăng dự án' : 'Xem tất cả';
  require APP_ROOT . '/app/views/partials/empty_state.php';
  ?>
<?php else: ?>
  <ul class="wl-list">
    <?php foreach ($projects as $p): ?>
      <li class="wl-item">
        <div class="wl-item__main">
          <h2 class="wl-item__title"><a href="<?= e(url('project/show') . '?id=' . (int) $p['ID']) ?>"><?= e($p['TieuDe']) ?></a></h2>
          <p class="wl-item__meta">
            <span>Ngân sách <strong><?= e(wl_money($p['NganSach'])) ?></strong></span>
            <span>Hạn nhận đề xuất <strong><?= e(wl_date($p['HanNhanDeXuat'])) ?></strong></span>
            <span>Đăng ngày <?= e(wl_date($p['NgayTao'])) ?></span>
          </p>
          <?php $skills = wl_skills($p['KyNang'] ?? ''); if ($skills): ?>
            <ul class="wl-chips" aria-label="Kỹ năng"><?php foreach ($skills as $s): ?><li class="wl-chip"><?= e($s) ?></li><?php endforeach; ?></ul>
          <?php endif; ?>
        </div>
        <div class="wl-item__side">
          <?= wl_badge('project', $p['TrangThai']) ?>
          <span class="wl-muted small"><?= (int) $p['SoDeXuat'] ?> đề xuất</span>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php
  $pagerUrl = $mineUrl;
  $pagerParams = $trangThai !== '' ? ['trangthai' => $trangThai] : [];
  require APP_ROOT . '/app/views/partials/pagination.php';
  ?>
<?php endif; ?>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
