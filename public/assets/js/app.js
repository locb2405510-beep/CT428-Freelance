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

  /* ---------- Điền sẵn dữ liệu vào modal (nút có data-fill-<tên trường>) ---------- */
  document.addEventListener('show.bs.modal', function (e) {
    var trigger = e.relatedTarget, modalNode = e.target;
    if (!trigger || !trigger.attributes) { return; }
    var form = modalNode.querySelector('form');
    if (!form) { return; }
    hideAlert(form);
    clearFieldErrors(form);
    Array.prototype.forEach.call(trigger.attributes, function (attr) {
      if (attr.name.indexOf('data-fill-') !== 0) { return; }
      var field = form.elements[attr.name.slice(10)];
      if (field) { field.value = attr.value; }
    });
    form.querySelectorAll('[data-money-preview]').forEach(function (el) {
      el.dispatchEvent(new Event('input', { bubbles: true }));
    });
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

  /* ---------- Tìm dự án (project/index): lọc bằng AJAX ---------- */
  function initSearch() {
    var form = document.getElementById('wl-search');
    if (!form) { return; }
    var endpoint = form.getAttribute('data-endpoint');
    var detailUrl = form.getAttribute('data-detail');
    var box = document.getElementById('wl-results');
    var info = document.getElementById('wl-result-info');
    var pager = document.getElementById('wl-search-pager');
    var fields = ['q', 'min', 'max', 'kynang', 'sort'];
    var timer = null, ticket = 0, page = 1;

    function el(tag, cls, text) {
      var n = document.createElement(tag);
      if (cls) { n.className = cls; }
      if (text !== undefined) { n.textContent = text; }
      return n;
    }
    function clear(node) { while (node.firstChild) { node.removeChild(node.firstChild); } }
    function money(n) { return formatMoney(parseInt(n, 10) || 0); }
    function date(s) { var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(s || ''); return m ? m[3] + '/' + m[2] + '/' + m[1] : ''; }

    function params(p) {
      var q = new URLSearchParams();
      fields.forEach(function (f) {
        var v = (form.elements[f].value || '').trim();
        if (v !== '' && !(f === 'sort' && v === 'newest')) { q.set(f, v); }
      });
      if (p > 1) { q.set('page', p); }
      return q;
    }

    function showSkeleton() {
      clear(box);
      box.setAttribute('aria-busy', 'true');
      for (var i = 0; i < 3; i++) { box.appendChild(el('div', 'wl-skeleton mb-3')); }
    }

    function renderEmpty() {
      clear(box);
      var wrap = el('div', 'wl-empty');
      wrap.setAttribute('role', 'status');
      wrap.appendChild(el('p', 'wl-empty__title', 'Không tìm thấy dự án phù hợp'));
      wrap.appendChild(el('p', 'wl-empty__text', 'Thử đổi từ khóa, nới khoảng ngân sách hoặc bỏ bớt bộ lọc.'));
      var btn = el('button', 'btn btn-outline-primary', 'Xóa bộ lọc');
      btn.type = 'button';
      btn.addEventListener('click', resetFilters);
      wrap.appendChild(btn);
      box.appendChild(wrap);
    }

    function renderError(message) {
      clear(box);
      var wrap = el('div', 'alert alert-danger d-flex flex-wrap align-items-center justify-content-between gap-2');
      wrap.setAttribute('role', 'alert');
      wrap.appendChild(el('span', '', message));
      var btn = el('button', 'btn btn-sm btn-outline-secondary', 'Thử lại');
      btn.type = 'button';
      btn.addEventListener('click', function () { load(page); });
      wrap.appendChild(btn);
      box.appendChild(wrap);
      info.textContent = '';
      clear(pager);
    }

    function card(it) {
      var li = el('li', 'wl-item');
      var main = el('div', 'wl-item__main');
      var h = el('h2', 'wl-item__title');
      var a = el('a', '', it.TieuDe);
      a.href = detailUrl + '?id=' + encodeURIComponent(it.ID);
      h.appendChild(a);
      main.appendChild(h);
      var meta = el('p', 'wl-item__meta');
      [['Ngân sách ', money(it.NganSach)], ['Hạn nhận đề xuất ', date(it.HanNhanDeXuat)], ['Khách hàng ', it.TenKhachHang || '']].forEach(function (pair) {
        var span = el('span', '', pair[0]);
        span.appendChild(el('strong', '', pair[1]));
        meta.appendChild(span);
      });
      main.appendChild(meta);
      var skills = (it.KyNang || '').split(',').map(function (x) { return x.trim(); }).filter(Boolean);
      if (skills.length) {
        var ul = el('ul', 'wl-chips');
        ul.setAttribute('aria-label', 'Kỹ năng');
        skills.forEach(function (sk) { ul.appendChild(el('li', 'wl-chip', sk)); });
        main.appendChild(ul);
      }
      var side = el('div', 'wl-item__side');
      side.appendChild(el('span', 'wl-muted small', (it.SoDeXuat || 0) + ' đề xuất'));
      var go = el('a', 'btn btn-outline-primary btn-sm', 'Xem chi tiết');
      go.href = a.href;
      side.appendChild(go);
      li.appendChild(main);
      li.appendChild(side);
      return li;
    }

    function pageBtn(label, target, opts) {
      var li = el('li', 'page-item' + (opts.disabled ? ' disabled' : '') + (opts.active ? ' active' : ''));
      var b = el('button', 'page-link', label);
      b.type = 'button';
      if (opts.active) { b.setAttribute('aria-current', 'page'); }
      if (opts.disabled) { b.disabled = true; } else { b.addEventListener('click', function () { load(target); window.scrollTo({ top: 0, behavior: 'smooth' }); }); }
      li.appendChild(b);
      return li;
    }

    function renderPager(cur, totalPages, total) {
      clear(pager);
      if (totalPages <= 1) { return; }
      var nav = el('nav', 'wl-pager');
      nav.setAttribute('aria-label', 'Phân trang');
      nav.appendChild(el('p', 'wl-pager__total', 'Tổng ' + total + ' dự án, trang ' + cur + ' / ' + totalPages));
      var ul = el('ul', 'pagination mb-0');
      ul.appendChild(pageBtn('Trước', cur - 1, { disabled: cur <= 1 }));
      var shown = [], prev = 0;
      for (var i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || Math.abs(i - cur) <= 2) { shown.push(i); }
      }
      shown.forEach(function (n) {
        if (prev && n - prev > 1) { ul.appendChild(pageBtn('…', n, { disabled: true })); }
        ul.appendChild(pageBtn(String(n), n, { active: n === cur }));
        prev = n;
      });
      ul.appendChild(pageBtn('Sau', cur + 1, { disabled: cur >= totalPages }));
      nav.appendChild(ul);
      pager.appendChild(nav);
    }

    function load(p) {
      var min = parseInt(form.elements.min.value, 10), max = parseInt(form.elements.max.value, 10);
      if (min > 0 && max > 0 && min > max) {
        renderError('Ngân sách "từ" phải nhỏ hơn hoặc bằng ngân sách "đến".');
        return;
      }
      page = p;
      var my = ++ticket;
      var q = params(p);
      var qs = q.toString();
      if (window.history && history.replaceState) {
        history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
      }
      showSkeleton();
      info.textContent = 'Đang tải...';
      clear(pager);
      fetch(endpoint + (qs ? '?' + qs : ''), {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      }).then(function (res) {
        return res.json().then(function (d) { return { status: res.status, d: d }; });
      }).then(function (r) {
        if (my !== ticket) { return; } // đã có yêu cầu mới hơn
        box.removeAttribute('aria-busy');
        if (!r.d || !r.d.success) {
          if (r.status === 401 && r.d && r.d.redirect) { window.location.href = r.d.redirect; return; }
          renderError((r.d && r.d.message) || 'Không tải được danh sách dự án.');
          return;
        }
        if (!r.d.items.length) { renderEmpty(); info.textContent = '0 dự án'; clear(pager); return; }
        clear(box);
        var ul = el('ul', 'wl-list');
        r.d.items.forEach(function (it) { ul.appendChild(card(it)); });
        box.appendChild(ul);
        info.textContent = 'Tìm thấy ' + r.d.total + ' dự án';
        renderPager(r.d.page, r.d.totalPages, r.d.total);
      }).catch(function () {
        if (my !== ticket) { return; }
        box.removeAttribute('aria-busy');
        renderError('Không kết nối được máy chủ. Vui lòng kiểm tra mạng và thử lại.');
      });
    }

    function resetFilters() {
      form.reset();
      load(1);
    }

    // điền lại bộ lọc từ địa chỉ trang (giữ khi tải lại hoặc gửi link)
    var initial = new URLSearchParams(window.location.search);
    fields.forEach(function (f) { if (initial.has(f)) { form.elements[f].value = initial.get(f); } });

    form.addEventListener('input', function (e) {
      if (e.target.name === 'sort') { return; }
      clearTimeout(timer);
      timer = setTimeout(function () { load(1); }, 300);
    });
    form.addEventListener('change', function (e) {
      if (e.target.name === 'sort') { load(1); }
    });
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      clearTimeout(timer);
      load(1);
    });
    var resetBtn = document.getElementById('wl-search-reset');
    if (resetBtn) { resetBtn.addEventListener('click', resetFilters); }

    load(parseInt(initial.get('page'), 10) || 1);
  }

  document.addEventListener('DOMContentLoaded', initSearch);

  window.WL = { submitAjax: submitAjax };
})();
