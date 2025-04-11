<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Admin Dashboard'; ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }

        .sidebar .nav-link {
            color: #adb5bd;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: #495057;
        }

        .content {
            min-height: 100vh;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="p-3">
                <h4 class="text-white">Admin Panel</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=admin&action=index">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=home&action=index">Trang chủ người dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=comic&action=index">Quản Lý Comics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=comic&action=deleted">Comic Đã Xóa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=category&action=index">Quản Lý Danh Mục</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=category&action=deleted">Danh Mục Đã Xóa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=order&action=index">Quản Lý Đơn Hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=user&action=index">Quản Lý Người Dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white mt-3"
                            href="index.php?controller=auth&action=logout">Đăng Xuất</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Nội dung chính -->
        <div class="content flex-grow-1 p-4">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php echo $content; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>