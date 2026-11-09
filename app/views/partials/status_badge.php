<?php
/** @var string $status Trạng thái dự án hoặc đề xuất. */
$labels = [
    'mo' => 'Đang mở',
    'dang_thuc_hien' => 'Đang thực hiện',
    'hoan_thanh' => 'Hoàn thành',
    'da_huy' => 'Đã hủy',
    'cho_duyet' => 'Chờ duyệt',
    'duoc_chon' => 'Được chọn',
    'tu_choi' => 'Từ chối',
];
$classes = [
    'mo' => 'text-bg-success',
    'dang_thuc_hien' => 'text-bg-primary',
    'hoan_thanh' => 'text-bg-secondary',
    'da_huy' => 'text-bg-danger',
    'cho_duyet' => 'text-bg-warning',
    'duoc_chon' => 'text-bg-success',
    'tu_choi' => 'text-bg-secondary',
];
$status = is_string($status ?? null) ? $status : '';
$label = $labels[$status] ?? 'Không xác định';
$class = $classes[$status] ?? 'text-bg-secondary';
?>
<span class="badge rounded-pill <?= e($class) ?>"><?= e($label) ?></span>
