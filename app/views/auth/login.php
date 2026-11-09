<?php
$title = $title ?? 'Đăng nhập';
require APP_ROOT . '/app/views/partials/header.php';
?>
<div class="wl-form-narrow">
  <h1 class="wl-auth-title">Đăng nhập</h1>
  <p class="wl-auth-sub">Chào mừng bạn quay lại WorkLance.</p>

  <div class="wl-card">
    <form action="<?= e(url('auth/doLogin')) ?>" method="post" data-ajax novalidate>
      <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
      <div class="form-alert d-none" role="alert"></div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" autocomplete="email" required autofocus>
      </div>
      <div class="mb-4">
        <label for="matkhau" class="form-label">Mật khẩu</label>
        <input type="password" class="form-control" id="matkhau" name="matkhau" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
    </form>
  </div>

  <p class="text-center mt-3 mb-0">Chưa có tài khoản? <a href="<?= e(url('auth/register')) ?>">Đăng ký ngay</a></p>
</div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
