<?php
// Biến: $title, $profile (ID, HoTen, Email, VaiTro, KyNang, NgayTao), $rating (DiemTB, SoLuot)
$title = $title ?? 'Hồ sơ';
require APP_ROOT . '/app/views/partials/header.php';
$isFreelancer = $profile['VaiTro'] === 'freelancer';
$initial = mb_strtoupper(mb_substr($profile['HoTen'], 0, 1, 'UTF-8'), 'UTF-8');
?>
<div class="wl-page-head">
  <div>
    <h1>Hồ sơ cá nhân</h1>
    <p>Cập nhật thông tin và mật khẩu của bạn.</p>
  </div>
</div>

<div class="wl-profile">
  <aside class="wl-card wl-profile__side">
    <span class="wl-avatar wl-avatar--lg" aria-hidden="true"><?= e($initial) ?></span>
    <h2 class="fs-5 fw-bold mb-1"><?= e($profile['HoTen']) ?></h2>
    <p class="wl-muted mb-2"><?= e($profile['Email']) ?></p>
    <span class="wl-role"><?= $isFreelancer ? 'Freelancer' : 'Khách hàng' ?></span>
    <hr>
    <p class="mb-1 wl-rating-text" aria-label="Điểm đánh giá trung bình"><?= (int) ($rating['SoLuot'] ?? 0) > 0 ? '★ ' : '' ?><?= e(wl_stars($rating['DiemTB'] ?? null, $rating['SoLuot'] ?? 0)) ?></p>
    <p class="wl-muted small mb-0">Tham gia từ <?= e(wl_date($profile['NgayTao'])) ?></p>
  </aside>

  <div>
    <section class="wl-card" aria-labelledby="thong-tin">
      <h2 class="wl-card__title" id="thong-tin">Thông tin cá nhân</h2>
      <form action="<?= e(url('profile/update')) ?>" method="post" data-ajax novalidate>
        <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
        <div class="form-alert d-none" role="alert"></div>
        <div class="mb-3">
          <label for="hoten" class="form-label">Họ và tên</label>
          <input type="text" class="form-control" id="hoten" name="hoten" minlength="2" maxlength="100" value="<?= e($profile['HoTen']) ?>" autocomplete="name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" value="<?= e($profile['Email']) ?>" disabled aria-describedby="email-help">
          <div id="email-help" class="form-text">Email dùng để đăng nhập nên không đổi được.</div>
        </div>
        <?php if ($isFreelancer): ?>
          <div class="mb-3">
            <label for="kynang" class="form-label">Kỹ năng</label>
            <input type="text" class="form-control" id="kynang" name="kynang" maxlength="500" value="<?= e($profile['KyNang'] ?? '') ?>" placeholder="php, mysql, thiết kế web" aria-describedby="kynang-help">
            <div id="kynang-help" class="form-text">Cách nhau bằng dấu phẩy.</div>
          </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
      </form>
    </section>

    <section class="wl-card" aria-labelledby="doi-mat-khau">
      <h2 class="wl-card__title" id="doi-mat-khau">Đổi mật khẩu</h2>
      <form action="<?= e(url('profile/password')) ?>" method="post" data-ajax data-reset novalidate>
        <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
        <div class="form-alert d-none" role="alert"></div>
        <div class="mb-3">
          <label for="matkhaucu" class="form-label">Mật khẩu hiện tại</label>
          <input type="password" class="form-control" id="matkhaucu" name="matkhaucu" autocomplete="current-password" required>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-sm-6">
            <label for="matkhaumoi" class="form-label">Mật khẩu mới</label>
            <input type="password" class="form-control" id="matkhaumoi" name="matkhaumoi" minlength="8" autocomplete="new-password" required>
          </div>
          <div class="col-sm-6">
            <label for="xacnhan" class="form-label">Nhập lại mật khẩu mới</label>
            <input type="password" class="form-control" id="xacnhan" name="xacnhan" minlength="8" autocomplete="new-password" required>
          </div>
        </div>
        <button type="submit" class="btn btn-outline-primary">Đổi mật khẩu</button>
      </form>
    </section>
  </div>
</div>
<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
