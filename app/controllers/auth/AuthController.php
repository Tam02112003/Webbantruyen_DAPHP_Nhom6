<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Đảm bảo đường dẫn đúng đến tệp autoload.php
class AuthController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    // Hiển thị trang đăng ký
    public function showRegisterForm() {
        $data = ['title' => 'Đăng Ký'];
        $this->view('/auth/register', $data);
    }

    public function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? ''; // Lấy mật khẩu xác nhận
        $errors = []; // Khởi tạo biến lỗi
    
        // Kiểm tra username
        if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
            $errors['username'] = "Tên đăng nhập phải từ 3 đến 20 ký tự và chỉ chứa chữ cái, số và dấu gạch dưới.";
        }
    
        // Kiểm tra email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Địa chỉ email không hợp lệ.";
        }
    
        // Kiểm tra mật khẩu
        if (empty($password) || strlen($password) < 8 || 
            !preg_match("/[A-Z]/", $password) || 
            !preg_match("/[a-z]/", $password) || 
            !preg_match("/[0-9]/", $password) || 
            !preg_match("/[\W_]/", $password)) {
            $errors['password'] = "Mật khẩu phải ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.";
        }
    
        // Kiểm tra mật khẩu xác nhận
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = "Mật khẩu xác nhận không khớp.";
        }
    
        // Nếu có lỗi, lưu lỗi vào session và chuyển hướng về form
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?controller=auth&action=showRegisterForm");
            exit;
        }
    
        // Kiểm tra xem tên người dùng đã tồn tại chưa
        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = "Tên người dùng đã tồn tại!";
            header("Location: index.php?controller=auth&action=showRegisterForm");
            exit;
        }
    
        // Đăng ký người dùng
        if ($this->userModel->register($username, $email, $password)) {
            $_SESSION['success'] = "Đăng ký thành công. Vui lòng đăng nhập.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        } else {
            $_SESSION['error'] = "Đăng ký không thành công!";
            header("Location: index.php?controller=auth&action=showRegisterForm");
            exit;
        }
    }
    public function loginWithGoogle() {
        if (isset($_SESSION['user'])) {
            // Người dùng đã đăng nhập qua Google
            header('Location: index.php?controller=home&action=index');
            exit;
        } else {
            // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập Google
            header('Location: login_with_google.php');
            exit;
        }
    }




    public function handleGoogleLogin($userInfo) {
        $email = $userInfo->email;
        $username = $this->generateUsernameFromEmail($email);
        $name = $userInfo->name ?? '';
        $picture = $userInfo->picture ?? '';
    
        // Kiểm tra và tạo tài khoản nếu chưa tồn tại
        if (!$this->userModel->userExists($email)) {
            $password = bin2hex(random_bytes(8));
            if (!$this->userModel->register($username, $email, password_hash($password, PASSWORD_BCRYPT))) {
                error_log("Failed to register Google user: " . $email);
                return false;
            }
        }
    
        // Lấy thông tin người dùng từ database (bao gồm cả role)
        $user = $this->userModel->getUserByEmail($email);
        if (!$user) {
            error_log("Google user not found after registration: " . $email);
            return false;
        }
    
        // Lưu thông tin vào session (bao gồm role từ database)
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'], // Lấy role từ database
            'google_picture' => $picture
        ];
    
        return true;
    }
    
    private function generateUsernameFromEmail($email) {
        return strtok($email, '@') . '_' . bin2hex(random_bytes(2));
    }

    // Hiển thị trang đăng nhập
    public function showLoginForm() {
        if (isset($_SESSION['user'])) {
            // Nếu người dùng đã đăng nhập, chuyển hướng đến trang chính
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        $data = ['title' => 'Đăng Nhập'];
        $this->view('/auth/login', $data);
    }
    // Xử lý đăng nhập

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
    
            // Xác thực người dùng
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                // Kiểm tra xem email có tồn tại trong kết quả trả về không
                if (!isset($user['email'])) {
                    // Nếu không có email, thử lấy từ database
                    $userWithEmail = $this->userModel->getUserByUsername($username);
                    if ($userWithEmail && isset($userWithEmail['email'])) {
                        $user['email'] = $userWithEmail['email'];
                    } else {
                        // Xử lý trường hợp không tìm thấy email
                        $user['email'] = ''; // hoặc giá trị mặc định
                    }
                }
    
                // Lưu thông tin người dùng vào session
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'], // Đảm bảo email được lưu
                    'role' => $user['role']
                ];
                
                // Kiểm tra session ngay sau khi lưu
                if (empty($_SESSION['user']['email'])) {
                    error_log("Không thể lấy email cho user: " . $username);
                }
    
                $_SESSION['success'] = "Đăng nhập thành công.";
                header('Location: index.php?controller=home&action=index');
                exit;
            } else {
                $_SESSION['error'] = "Tên người dùng hoặc mật khẩu không đúng.";
                header('Location: index.php?controller=auth&action=showLoginForm');
                exit;
            }
        }
    }
    
    // Xử lý đăng xuất
    public function logout() {
        session_destroy();
        header("Location: index.php?controller=home&action=index");
        exit;
    }
    private function view($viewPath, $data = []) {
        ob_start();
        extract($data);
        $file = 'app/views' . $viewPath . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            die("Lỗi: Không tìm thấy tệp view: " . $file);
        }
        $content = ob_get_clean();

        $layoutData = array_merge($data, ['content' => $content]);
        extract($layoutData);
        $layoutFile = 'app/views/layouts/customer_layout.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            die("Lỗi: Không tìm thấy tệp layout: " . $layoutFile);
        }
    }
}
?>