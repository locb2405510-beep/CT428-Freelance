<?php
$title = $title ?? 'Đăng ký';
require APP_ROOT . '/app/views/partials/header.php';
?>
<div class="wl-form-narrow">
  <h1 class="wl-auth-title">Tạo tài khoản</h1>
  <p class="wl-auth-sub">Chọn vai trò phù hợp để bắt đầu.</p>

  <div class="wl-card">
    <form action="<?= e(url('auth/doRegister')) ?>" method="post" data-ajax novalidate>
      <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
      <div class="form-alert d-none" role="alert"></div>

      <fieldset class="mb-3">
        <legend class="form-label fs-6">Bạn là</legend>
        <div class="wl-role-choice">
          <div>
            <input type="radio" name="vaitro" id="vaitro-khachhang" value="khachhang" checked>
            <label for="vaitro-khachhang"><strong>Khách hàng</strong><span>Đăng dự án và chọn người làm</span></label>
          </div>
          <div>
            <input type="radio" name="vaitro" id="vaitro-freelancer" value="freelancer">
            <label for="vaitro-freelancer"><strong>Freelancer</strong><span>Tìm dự án và gửi đề xuất</span></label>
          </div>
        </div>
        <div class="text-danger small mt-1 d-none" data-error-for="vaitro"></div>
      </fieldset>

      <div class="mb-3">
        <label for="hoten" class="form-label">Họ và tên</label>
        <input type="text" class="form-control" id="hoten" name="hoten" minlength="2" maxlength="100" autocomplete="name" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" autocomplete="email" required>
      </div>
      <div class="mb-3">
        <label for="matkhau" class="form-label">Mật khẩu</label>
        <input type="password" class="form-control" id="matkhau" name="matkhau" minlength="8" autocomplete="new-password" required aria-describedby="matkhau-help">
        <div id="matkhau-help" class="form-text">Từ 8 ký tự trở lên.</div>
      </div>
      <div class="mb-3">
        <label for="xacnhan" class="form-label">Nhập lại mật khẩu</label>
        <input type="password" class="form-control" id="xacnhan" name="xacnhan" minlength="8" autocomplete="new-password" required>
      </div>
      <div class="mb-4 d-none" data-show-role="freelancer">
        <label for="kynang" class="form-label">Kỹ năng <span class="wl-muted fw-normal">(không bắt buộc)</span></label>
        <input type="text" class="form-control" id="kynang" name="kynang" maxlength="500" placeholder="php, mysql, thiết kế web" aria-describedby="kynang-help">
        <div id="kynang-help" class="form-text">Cách nhau bằng dấu phẩy.</div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
    </form>
  </div>

  <p class="text-center mt-3 mb-0">Đã có tài khoản? <a href="<?= e(url('auth/login')) ?>">Đăng nhập</a></p>
</div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
