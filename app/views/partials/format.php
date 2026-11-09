<?php
// Hàm định dạng dùng chung cho view (quy ước mục 1.5 của bảng hợp đồng).

if (!function_exists('wl_money')) {
    function wl_money($amount): string
    {
        return number_format((float) $amount, 0, ',', '.') . ' ₫';
    }
}

if (!function_exists('wl_date')) {
    function wl_date(?string $date): string
    {
        $time = $date ? strtotime($date) : false;
        return $time ? date('d/m/Y', $time) : '';
    }
}

if (!function_exists('wl_skills')) {
    /** Tách chuỗi kỹ năng cách nhau bằng dấu phẩy thành mảng. */
    function wl_skills(?string $skills): array
    {
        if ($skills === null || trim($skills) === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $skills)), 'strlen'));
    }
}

if (!function_exists('wl_status_map')) {
    function wl_status_map(string $type): array
    {
        if ($type === 'proposal') {
            return [
                'cho_duyet' => 'Chờ duyệt',
                'duoc_chon' => 'Được chọn',
                'tu_choi'   => 'Bị từ chối',
            ];
        }
        return [
            'mo'             => 'Đang nhận đề xuất',
            'dang_thuc_hien' => 'Đang thực hiện',
            'hoan_thanh'     => 'Hoàn thành',
            'da_huy'         => 'Đã hủy',
        ];
    }
}

if (!function_exists('wl_badge')) {
    /** Trả về HTML nhãn trạng thái. $type là 'project' hoặc 'proposal'. */
    function wl_badge(string $type, ?string $code): string
    {
        $labels = wl_status_map($type);
        $code = (string) $code;
        $label = $labels[$code] ?? $code;
        return '<span class="wl-badge wl-badge--' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
    }
}

if (!function_exists('wl_is_open_for_proposals')) {
    /** Dự án còn nhận đề xuất: trạng thái mo và hạn lớn hơn hoặc bằng hôm nay. */
    function wl_is_open_for_proposals(array $project): bool
    {
        return ($project['TrangThai'] ?? '') === 'mo'
            && ($project['HanNhanDeXuat'] ?? '') >= date('Y-m-d');
    }
}

if (!function_exists('wl_stars')) {
    /** Điểm trung bình dạng chữ, ví dụ "4,5 (12 lượt)". Trả về "Chưa có đánh giá" nếu rỗng. */
    function wl_stars($avg, $count): string
    {
        $count = (int) $count;
        if ($count < 1 || $avg === null) {
            return 'Chưa có đánh giá';
        }
        return number_format((float) $avg, 1, ',', '.') . ' / 5 (' . $count . ' lượt)';
    }
}
