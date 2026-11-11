/*
 * WorkLance - JavaScript chung.
 * - Form có data-ajax: gửi bằng fetch, đọc JSON {success, message, errors?, redirect?}.
 * - Form có data-confirm="câu hỏi": hiện hộp thoại xác nhận trước khi gửi.
 * - Mọi dữ liệu hiển thị bằng textContent, không dùng innerHTML với dữ liệu người dùng.
 */
(function () {
  'use strict';

  var REDIRECT_DELAY = 700;

  /* ---------- Thông báo trong form ---------- */
  function getAlert(form) {
    return form.querySelector('.form-alert');
  }

  function showAlert(form, type, message) {
    var box = getAlert(form);
    if (!box) { return; }
    box.className = 'form-alert alert alert-' + type;
    box.setAttribute('role', 'alert');
    box.textContent = message;
    if (box.scrollIntoView) { box.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
  }

  function hideAlert(form) {
    var box = getAlert(form);
    if (box) { box.className = 'form-alert d-none'; box.textContent = ''; }
  }

  /* ---------- Lỗi từng ô nhập ---------- */
  function clearFieldErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
    form.querySelectorAll('.invalid-feedback[data-generated]').forEach(function (el) { el.remove(); });
    form.querySelectorAll('[data-error-for]').forEach(function (el) { el.textContent = ''; el.classList.add('d-none'); });
  }

  function setFieldError(form, name, message) {
    var slot = form.querySelector('[data-error-for="' + name + '"]');
    var field = form.querySelector('[name="' + name + '"]');
    if (field && field.type !== 'radio') { field.classList.add('is-invalid'); }
    if (slot) {
      slot.textContent = message;
      slot.classList.remove('d-none');
      return;
    }
    if (!field) { return; }
    var hint = document.createElement('div');
    hint.className = 'invalid-feedback';
    hint.setAttribute('data-generated', '1');
    hint.textContent = message;
    field.insertAdjacentElement('afterend', hint);
  }

  function showFieldErrors(form, errors) {
    var first = null;
    Object.keys(errors || {}).forEach(function (name) {
      setFieldError(form, name, errors[name]);
      if (!first) { first = form.querySelector('[name="' + name + '"]'); }
    });
    if (first && first.type !== 'radio' && first.focus) { first.focus({ preventScroll: false }); }
  }

  /* ---------- Kiểm tra nhanh phía trình duyệt (server vẫn kiểm tra lại) ---------- */
  function labelOf(field) {
    var id = field.id, label = id ? document.querySelector('label[for="' + id + '"]') : null;
    return label ? label.textContent.trim().replace(/\s*\*$/, '').toLowerCase() : 'trường này';
  }

  function clientValidate(form) {
    var errors = {}, count = 0;
    form.querySelectorAll('input, textarea, select').forEach(function (f) {
      if (!f.name || f.name === 'csrf' || f.disabled || f.type === 'hidden' || errors[f.name]) { return; }
      if (f.closest('[hidden], .d-none')) { return; }
      var v = (f.value || '').trim();
      var msg = '';
      if (f.type === 'radio') {
        var group = form.querySelectorAll('input[type="radio"][name="' + f.name + '"]');
        var chosen = Array.prototype.some.call(group, function (r) { return r.checked; });
        if (f.required && !chosen) { msg = 'Vui lòng chọn một mục.'; }
      } else if (f.required && v === '') {
        msg = 'Vui lòng nhập ' + labelOf(f) + '.';
      } else if (v !== '' && f.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
        msg = 'Email không đúng định dạng.';
      } else if (v !== '' && f.minLength > 0 && v.length < f.minLength) {
        msg = 'Cần ít nhất ' + f.minLength + ' ký tự.';
      } else if (v !== '' && f.type === 'number' && f.min !== '' && Number(v) < Number(f.min)) {
        msg = 'Giá trị phải từ ' + f.min + ' trở lên.';
      } else if (v !== '' && f.type === 'number' && f.max !== '' && Number(v) > Number(f.max)) {
        msg = 'Giá trị không được lớn hơn ' + f.max + '.';
      }
      if (msg) { errors[f.name] = msg; count++; }
    });
    return count ? errors : null;
  }

  /* ---------- Gửi form ---------- */
  function setBusy(form, busy) {
    var btn = form.querySelector('button[type="submit"]');
    if (!btn) { return; }
    if (busy) {
      btn.dataset.label = btn.textContent;
      btn.disabled = true;
      btn.classList.add('btn-loading');
      var spin = document.createElement('span');
      spin.className = 'spinner-border';
      spin.setAttribute('aria-hidden', 'true');
      btn.textContent = '';
      btn.appendChild(spin);
      btn.appendChild(document.createTextNode('Đang xử lý...'));
    } else {
      btn.disabled = false;
      btn.classList.remove('btn-loading');
      btn.textContent = btn.dataset.label || btn.textContent;
    }
  }

  function submitAjax(form) {
    hideAlert(form);
    clearFieldErrors(form);

    var local = clientValidate(form);
    if (local) {
      showAlert(form, 'danger', 'Vui lòng kiểm tra lại thông tin.');
      showFieldErrors(form, local);
      return;
    }

    setBusy(form, true);
    var done = false; // true khi sắp chuyển trang, giữ nút ở trạng thái đang xử lý

    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(function (res) {
      return res.text().then(function (text) {
        var data = null;
        try { data = JSON.parse(text); } catch (e) { data = null; }
        return { status: res.status, data: data };
      });
    }).then(function (r) {
      var data = r.data;
      if (!data) {
        showAlert(form, 'danger', 'Có lỗi hệ thống, vui lòng thử lại.');
        return;
      }
      if (data.success) {
        showAlert(form, 'success', data.message || 'Thành công.');
        if (form.hasAttribute('data-reset')) { form.reset(); }
        if (data.redirect) {
          done = true;
          setTimeout(function () { window.location.href = data.redirect; }, REDIRECT_DELAY);
        }
        form.dispatchEvent(new CustomEvent('ajax:success', { bubbles: true, detail: data }));
        return;
      }
      showAlert(form, 'danger', data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
      if (data.errors) { showFieldErrors(form, data.errors); }
      if (r.status === 401 && data.redirect) {
        done = true;
        setTimeout(function () { window.location.href = data.redirect; }, 1200);
      }
    }).catch(function () {
      showAlert(form, 'danger', 'Không kết nối được máy chủ. Vui lòng kiểm tra mạng và thử lại.');
    }).then(function () {
      if (!done) { setBusy(form, false); }
    });
  }

  /* ---------- Hộp thoại xác nhận (data-confirm) ---------- */
  var pendingForm = null;
  var modalEl, modal;

  function askConfirm(form) {
    modalEl = modalEl || document.getElementById('wl-confirm');
    if (!modalEl || !window.bootstrap) {
      submitAjax(form);
      return;
    }
    modal = modal || new window.bootstrap.Modal(modalEl);
    pendingForm = form;
    document.getElementById('wl-confirm-text').textContent = form.getAttribute('data-confirm');
    var ok = document.getElementById('wl-confirm-ok');
    var danger = form.hasAttribute('data-danger');
    ok.className = 'btn ' + (danger ? 'btn-danger' : 'btn-primary');
    ok.textContent = form.getAttribute('data-confirm-ok') || 'Đồng ý';
    modal.show();
  }

  document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'wl-confirm-ok' && pendingForm) {
      var form = pendingForm;
      pendingForm = null;
      modal.hide();
      submitAjax(form);
    }
  });

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.matches || !form.matches('form[data-ajax]')) { return; }
    e.preventDefault();
    if (form.hasAttribute('data-confirm')) { askConfirm(form); } else { submitAjax(form); }
  });

  /* ---------- Hiện số tiền đã định dạng dưới ô nhập ---------- */
  function formatMoney(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' ₫';
  }

  document.addEventListener('input', function (e) {
    var el = e.target;
    if (el.matches && el.matches('[data-money-preview]')) {
      var slot = document.getElementById(el.getAttribute('data-money-preview'));
      var n = parseInt(el.value, 10);
      if (slot) { slot.textContent = n > 0 ? formatMoney(n) : ''; }
    }
  });

  /* ---------- Hiện hoặc ẩn khối theo vai trò đã chọn ---------- */
  function syncRoleBlocks(scope) {
    scope.querySelectorAll('[data-show-role]').forEach(function (block) {
      var wanted = block.getAttribute('data-show-role');
      var form = block.closest('form');
      var chosen = form && form.querySelector('input[name="vaitro"]:checked');
      block.classList.toggle('d-none', !chosen || chosen.value !== wanted);
    });
  }

  document.addEventListener('change', function (e) {
    if (e.target && e.target.name === 'vaitro') { syncRoleBlocks(document); }
  });

  document.addEventListener('DOMContentLoaded', function () {
    syncRoleBlocks(document);
    document.querySelectorAll('[data-money-preview]').forEach(function (el) {
      el.dispatchEvent(new Event('input', { bubbles: true }));
    });
  });

  window.WL = { submitAjax: submitAjax };
})();
