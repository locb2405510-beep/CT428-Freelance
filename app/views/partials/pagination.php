<?php
// Phân trang dùng chung (10 mục mỗi trang, tham số page).
// Cần: $page, $totalPages, $total, $pagerUrl (đường dẫn gốc, tạo bằng url()).
// Tùy chọn: $pagerParams (mảng tham số giữ lại, ví dụ ['trangthai' => 'mo']).
$page       = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$total      = (int) ($total ?? 0);
$pagerParams = $pagerParams ?? [];

$pageLink = function (int $p) use ($pagerUrl, $pagerParams): string {
    $params = $pagerParams;
    unset($params['page']);
    if ($p > 1) {
        $params['page'] = $p;
    }
    return $pagerUrl . ($params ? '?' . http_build_query($params) : '');
};

// Hiện trang đầu, trang cuối và hai trang quanh trang hiện tại.
$pages = array_unique(array_filter(
    array_merge([1, $totalPages], range($page - 2, $page + 2)),
    fn($p) => $p >= 1 && $p <= $totalPages
));
sort($pages);
?>
<?php if ($totalPages > 1): ?>
<nav class="wl-pager" aria-label="Phân trang">
  <p class="wl-pager__total">Tổng <?= $total ?> mục, trang <?= $page ?> / <?= $totalPages ?></p>
  <ul class="pagination mb-0">
    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
      <a class="page-link" href="<?= e($pageLink(max(1, $page - 1))) ?>"<?= $page <= 1 ? ' tabindex="-1" aria-disabled="true"' : '' ?>>Trước</a>
    </li>
    <?php $prev = 0; foreach ($pages as $p): ?>
      <?php if ($prev && $p - $prev > 1): ?>
        <li class="page-item disabled"><span class="page-link">…</span></li>
      <?php endif; ?>
      <li class="page-item<?= $p === $page ? ' active' : '' ?>">
        <a class="page-link" href="<?= e($pageLink($p)) ?>"<?= $p === $page ? ' aria-current="page"' : '' ?>><?= $p ?></a>
      </li>
    <?php $prev = $p; endforeach; ?>
    <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
      <a class="page-link" href="<?= e($pageLink(min($totalPages, $page + 1))) ?>"<?= $page >= $totalPages ? ' tabindex="-1" aria-disabled="true"' : '' ?>>Sau</a>
    </li>
  </ul>
</nav>
<?php endif; ?>
