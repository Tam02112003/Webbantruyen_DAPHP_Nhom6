<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/register.css">
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="register-logo">
                <i class="fas fa-book-open"></i>
                <span>Comic Store</span>
            </div>
            <h1>Tạo tài khoản mới</h1>
            <p>Tham gia cộng đồng yêu truyện tranh của chúng tôi</p>
        </div>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="error-list">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <div class="error-item"><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=register" class="register-form" id="registerForm">
            <div class="form-group">
                <label for="username" class="form-label">
                    <i class="fas fa-user"></i> Tên người dùng
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Nhập tên người dùng (5-20 ký tự)" required>
                    <span class="input-icon valid-icon">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
                <small class="form-text">Tên người dùng phải từ 5-20 ký tự, không chứa ký tự đặc biệt</small>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <div class="input-group">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Nhập địa chỉ email" required>
                    <span class="input-icon valid-icon">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Mật khẩu
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Nhập mật khẩu (ít nhất 8 ký tự)" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-bar"></div>
                    <span class="strength-text">Độ mạnh mật khẩu</span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock"></i> Xác nhận mật khẩu
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Nhập lại mật khẩu" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                    <span class="input-icon match-icon">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
                <div class="password-match">
                    <i class="fas fa-check-circle"></i> <span>Mật khẩu khớp</span>
                </div>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                <label class="form-check-label" for="agreeTerms">
                    Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Điều khoản dịch vụ</a>
                </label>
            </div>

            <button type="submit" class="btn btn-register">
                <i class="fas fa-user-plus"></i> Đăng ký
            </button>

            <div class="login-link">
                Đã có tài khoản? 
                <a href="index.php?controller=auth&action=showLoginForm">Đăng nhập ngay</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal Điều khoản dịch vụ -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Điều khoản dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nội dung điều khoản dịch vụ -->
                <p>Đây là nội dung điều khoản dịch vụ của Comic Store...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tôi hiểu</button>
            </div>
        </div>
    </div>
</div>

<script src="public/js/password.js"></script>
</body>
</html>