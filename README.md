# Dự án Freelance Platform

Dưới đây là cấu trúc thư mục của dự án:

```text
freelance-platform/
├── database/
│   └── schema.sql
├── app/
│   ├── config/
│   │   ├── config.php          # BASE_URL, APP_ROOT
│   │   └── database.php        # thông tin kết nối
│   ├── core/
│   │   ├── Database.php        # PDO singleton
│   │   ├── Controller.php      # lớp cha: view(), json(), requireLogin()...
│   │   ├── Csrf.php
│   │   └── helpers.php         # e(), url(), redirect()
│   ├── controllers/
│   │   ├── AuthController.php  # đăng ký / đăng nhập / đăng xuất
│   │   ├── ProjectController.php
│   │   ├── ProposalController.php   # (các bước sau)
│   │   └── ProfileController.php    # (các bước sau)
│   ├── models/
│   │   ├── User.php
│   │   ├── Project.php
│   │   ├── Proposal.php        # (các bước sau)
│   │   └── Review.php          # (các bước sau)
│   └── views/
│       ├── partials/header.php, footer.php
│       ├── auth/login.php, register.php
│       └── projects/create.php
└── public/                     # chỉ thư mục này được truy cập từ web
    ├── index.php               # front controller (router)
    ├── .htaccess
    └── assets/
        ├── css/style.css
        └── js/app.js
```

## Phân công giao diện

Lộc phụ trách `app/views/**` và `public/assets/**`. Mọi thay đổi giao diện đi qua nhánh `feature/loc-<việc>` và Pull Request vào `develop`.
