<?php
// Nhãn trạng thái dùng chung.
// Cần: $status (hoặc $badgeCode) là mã trạng thái. $badgeType ('project' hoặc 'proposal') là tùy chọn,
// bỏ trống thì tự nhận biết theo mã.
require_once __DIR__ . '/format.php';
$badgeCode = $badgeCode ?? (is_string($status ?? null) ? $status : '');
if (!isset($badgeType)) {
    $badgeType = array_key_exists($badgeCode, wl_status_map('proposal')) ? 'proposal' : 'project';
}
echo wl_badge($badgeType, $badgeCode);
