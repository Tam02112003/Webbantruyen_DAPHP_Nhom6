<?php
require_once 'app/models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    private function checkAdmin() {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Bạn cần đăng nhập để truy cập trang này.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }

        // Kiểm tra vai trò
        if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = "Bạn cần quyền admin để truy cập trang này.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    public function index() {
        $this->checkAdmin();
        $users = $this->userModel->getAllUsers();
        $data = [
            'users' => $users,
            'title' => 'Quản Lý Người Dùng'
        ];
        $this->view('/admin/users/index', $data);
    }

    public function updateRole($userId) {
        $this->checkAdmin();
        $role = $_POST['role'] ?? '';
        $result = $this->userModel->updateRole($userId, $role);
        if ($result === true) {
            $_SESSION['success'] = "Cập nhật vai trò thành công.";
        } else {
            $_SESSION['error'] = $result['error'] ?? "Không thể cập nhật vai trò. Vui lòng thử lại.";
        }
        header('Location: index.php?controller=user&action=index');
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
        $layoutFile = 'app/views/layouts/admin_layout.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            die("Lỗi: Không tìm thấy tệp layout: " . $layoutFile);
        }
    }
}
?>