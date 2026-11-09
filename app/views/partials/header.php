<?php
// Phần đầu trang dùng chung. View gọi: require APP_ROOT . '/app/views/partials/header.php';
// Biến tùy chọn: $title (tiêu đề trang).
require_once __DIR__ . '/format.php';

$wlUser  = function_exists('current_user') ? current_user() : null;
$wlRole  = $wlUser['role'] ?? null;
$wlPath  = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$wlActive = fn(string $route): string => str_contains($wlPath, '/' . $route) ? ' active' : '';

if ($wlRole === 'khachhang') {
    $wlNav = [
        ['project/mine',   'Dự án của tôi'],
        ['project/create', 'Đăng dự án'],
        ['profile/index',  'Hồ sơ'],
    ];
} elseif ($wlRole === 'freelancer') {
    $wlNav = [
        ['project/index', 'Tìm dự án'],
        ['proposal/mine', 'Đề xuất của tôi'],
        ['profile/index', 'Hồ sơ'],
    ];
} else {
    $wlNav = [];
}
$wlTitle = !empty($title) ? $title . ' | WorkLance' : 'WorkLance';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($wlTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body>
<a class="wl-skip" href="#main">Bỏ qua điều hướng</a>

<header class="wl-header">
  <nav class="navbar navbar-expand-md" aria-label="Điều hướng chính">
    <div class="container">
      <a class="navbar-brand wl-brand" href="<?= e(url('')) ?>">WorkLance</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#wl-nav"
              aria-controls="wl-nav" aria-expanded="false" aria-label="Mở menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="wl-nav">
        <ul class="navbar-nav me-auto mb-2 mb-md-0">
          <?php foreach ($wlNav as [$route, $label]): ?>
            <li class="nav-item">
              <a class="nav-link<?= $wlActive($route) ?>" href="<?= e(url($route)) ?>"><?= e($label) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="d-flex align-items-md-center flex-column flex-md-row gap-2">
          <?php if ($wlUser): ?>
            <span class="wl-user" title="<?= e($wlUser['email'] ?? '') ?>">
              <span class="wl-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($wlUser['name'] ?? '?', 0, 1, 'UTF-8'), 'UTF-8')) ?></span>
              <span><?= e($wlUser['name'] ?? '') ?></span>
              <span class="wl-role"><?= $wlRole === 'freelancer' ? 'Freelancer' : 'Khách hàng' ?></span>
            </span>
            <form action="<?= e(url('auth/logout')) ?>" method="post" data-ajax novalidate>
              <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
              <button type="submit" class="btn btn-outline-secondary btn-sm">Đăng xuất</button>
            </form>
          <?php else: ?>
            <a class="btn btn-outline-primary btn-sm" href="<?= e(url('auth/login')) ?>">Đăng nhập</a>
            <a class="btn btn-primary btn-sm" href="<?= e(url('auth/register')) ?>">Đăng ký</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
</header>

<main id="main" class="wl-main container">
