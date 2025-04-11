<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
    

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-book-open"></i>
                <span>Comic Store</span>
            </div>
            <h1>Đăng Nhập</h1>
            <p>Vui lòng đăng nhập để tiếp tục</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=login" class="login-form">
            <div class="form-group">
                <label for="username" class="form-label">
                    <i class="fas fa-user"></i> Tên Người Dùng
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên người dùng" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Mật Khẩu
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <!-- <div class="text-end mt-2">
                    <a href="index.php?controller=auth&action=forgotPassword" class="forgot-password">Quên mật khẩu?</a>
                </div> -->
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
            </button>

            <div class="login-divider">
                <span>hoặc</span>
            </div>

            <a href="login_with_google.php" class="btn btn-google">
                <i class="fab fa-google"></i> Đăng Nhập Bằng Google
            </a>

            <div class="register-link">
                Chưa có tài khoản? 
                <a href="index.php?controller=auth&action=showRegisterForm">Đăng ký ngay</a>
            </div>
        </form>
    </div>
</div>



<script src="public/js/password.js"></script>
</body>
</html>