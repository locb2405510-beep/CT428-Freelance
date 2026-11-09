</main>

<footer class="wl-footer">
  <div class="container">
    <span>WorkLance · Đồ án CT428, Nền tảng Kết nối Việc làm Tự do</span>
  </div>
</footer>

<div class="modal fade" id="wl-confirm" tabindex="-1" aria-labelledby="wl-confirm-title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="wl-confirm-title">Xác nhận thao tác</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body" id="wl-confirm-text"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Không</button>
        <button type="button" class="btn btn-primary" id="wl-confirm-ok">Đồng ý</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(url('assets/js/app.js')) ?>"></script>
</body>
</html>
