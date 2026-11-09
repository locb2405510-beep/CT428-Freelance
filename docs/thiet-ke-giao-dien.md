# Thiết kế giao diện

Phần của Lộc (Frontend và UI/UX). Mã nguồn nằm trong `app/views/**` và `public/assets/**`.

## 1. Định hướng

- Công nghệ: HTML5, Bootstrap 5 (CDN), CSS riêng trong `public/assets/css/style.css`, JavaScript thuần trong `public/assets/js/app.js` (dùng `fetch`).
- Màu chủ đạo xanh ngọc `#0f766e`, nền `#f3f6f8`, thẻ nội dung trắng bo góc 12 đến 16px. Toàn bộ màu và bán kính khai báo thành biến `:root` để đổi một chỗ.
- Tiếng Việt có dấu, câu chữ ngắn, nút nêu rõ hành động ("Đăng dự án", "Gửi đề xuất").
- Hai vai trò có thanh điều hướng riêng: khách hàng (Dự án của tôi, Đăng dự án, Hồ sơ) và freelancer (Tìm dự án, Đề xuất của tôi, Hồ sơ).

## 2. Thành phần dùng chung

| Thành phần | File | Công dụng |
|---|---|---|
| Đầu trang, chân trang | `partials/header.php`, `footer.php` | Menu theo vai trò, hộp thoại xác nhận dùng chung |
| Nhãn trạng thái | `partials/status_badge.php`, `format.php` | Màu thống nhất cho trạng thái dự án và đề xuất |
| Phân trang | `partials/pagination.php` | 10 mục mỗi trang, giữ bộ lọc trên địa chỉ |
| Trạng thái rỗng | `partials/empty_state.php` | Thông báo kèm nút hành động khi chưa có dữ liệu |
| Trang lỗi | `errors/403.php`, `404.php` | Trang lỗi cùng kiểu giao diện |

Mã trạng thái và màu: dự án `mo` xanh lá, `dang_thuc_hien` xanh dương, `hoan_thanh` xanh ngọc đậm, `da_huy` xám; đề xuất `cho_duyet` vàng, `duoc_chon` xanh lá, `tu_choi` xám.

## 3. Gửi form bằng AJAX

Mọi form ghi dữ liệu có `data-ajax`, trường ẩn `csrf` và vùng `.form-alert`. `app.js` làm các việc sau:

1. Kiểm tra nhanh phía trình duyệt (ô trống, email, độ dài tối thiểu) để báo lỗi ngay. Server vẫn kiểm tra lại.
2. Gửi `fetch` kèm `X-Requested-With`, nút chuyển sang trạng thái "Đang xử lý..." để tránh bấm hai lần.
3. Đọc JSON `{success, message, errors?, redirect?}`: hiện thông báo, gắn lỗi dưới từng ô theo khóa trong `errors`, tự chuyển trang nếu có `redirect`.
4. Form có `data-confirm` mở hộp thoại xác nhận trước khi gửi (hủy, xóa, hoàn thành, chọn hoặc rút đề xuất).

## 4. Các màn hình

| Màn hình | Vai trò | Ghi chú |
|---|---|---|
| Đăng nhập, đăng ký | khách | Chọn vai trò; ô kỹ năng chỉ hiện cho freelancer |
| Đăng dự án, sửa dự án | khách hàng | Xem trước số tiền định dạng khi nhập ngân sách |
| Dự án của tôi | khách hàng | Lọc theo trạng thái, hiện số đề xuất của từng dự án |
| Chi tiết dự án | khách hàng | Nút Sửa, Xóa, Hủy, Hoàn thành hiện theo trạng thái; chọn đề xuất |
| Tìm dự án | freelancer | Lọc AJAX (trễ 300ms), sắp xếp, phân trang, trạng thái đang tải và rỗng |
| Chi tiết dự án, gửi đề xuất | freelancer | Dự án đóng hoặc hết hạn thì khóa form |
| Đề xuất của tôi | freelancer | Sửa bằng cửa sổ, rút đề xuất có xác nhận |
| Hồ sơ | cả hai | Hai form riêng: thông tin và mật khẩu; hiện điểm đánh giá |
| Đánh giá | cả hai | Chọn 1 đến 5 sao, nhận xét tối đa 500 ký tự |

Ảnh chụp từng màn hình nằm ở `docs/screenshots/` (xem ghi chú trong thư mục đó).

## 5. An toàn và truy cập

- Dữ liệu in ra HTML đều qua `e()`. JavaScript dựng giao diện bằng `textContent`, không dùng `innerHTML` với dữ liệu người dùng. Đã thử chèn `<img onerror>` và `<script>` vào tiêu đề, mô tả, tên, nội dung đề xuất: tất cả hiện thành chữ.
- Mọi ô nhập có `label`, có liên kết "Bỏ qua điều hướng", dùng được bằng phím Tab, trạng thái focus rõ, tôn trọng `prefers-reduced-motion`.

## 6. Responsive

Kiểm tra ở 360, 768 và 1280px: không cuộn ngang, danh sách chuyển thành một cột trên điện thoại, bộ lọc xếp dọc, thanh điều hướng thu gọn thành nút menu.
