<?php

class AdminController {


    private function checkAdmin()
    {
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
        $this->view('/admin/index');
  
    } 
    private function view($viewPath)
    {
       
        ob_start();
        $file = 'app/views' . $viewPath . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            die("Lỗi: Không tìm thấy tệp view: " . $file);
        }
        $content = ob_get_clean();

        $layoutData = array_merge( ['content' => $content]);
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