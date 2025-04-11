<?php
require_once 'app/models/CategoryModel.php';

class CategoryController {
    private $categoryModel;

    public function __construct($db) {
        $this->categoryModel = new CategoryModel($db);
    }

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

        $categories = $this->categoryModel->getCategories();
        if ($categories === false) {
            $_SESSION['error'] = "Không thể lấy danh sách danh mục. Vui lòng thử lại sau.";
            $categories = [];
        }

        $data = [
            'categories' => $categories,
            'title' => 'Quản Lý Danh Mục'
        ];
        $this->view('/admin/categories/index', $data);
    }

    public function add() {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $errors = [];

            if (empty($name)) {
                $errors['name'] = "Tên danh mục không được để trống.";
            }

            if (empty($errors)) {
                $result = $this->categoryModel->addCategory($name);
                if ($result === true) {
                    $_SESSION['success'] = "Thêm danh mục thành công.";
                    header('Location: index.php?controller=category&action=index');
                    exit;
                } else {
                    $errors['database'] = "Có lỗi xảy ra khi thêm danh mục. Vui lòng thử lại.";
                }
            }

            $data = [
                'errors' => $errors,
                'title' => 'Thêm Danh Mục'
            ];
            $this->view('/admin/categories/add', $data);
        } else {
            $data = [
                'title' => 'Thêm Danh Mục'
            ];
            $this->view('/admin/categories/add', $data);
        }
    }

    public function edit($id) {
        $this->checkAdmin();

        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $_SESSION['error'] = "Danh mục không tồn tại hoặc đã bị xóa.";
            header('Location: index.php?controller=category&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $errors = [];

            if (empty($name)) {
                $errors['name'] = "Tên danh mục không được để trống.";
            }

            if (empty($errors)) {
                $result = $this->categoryModel->updateCategory($id, $name);
                if ($result === true) {
                    $_SESSION['success'] = "Cập nhật danh mục thành công.";
                    header('Location: index.php?controller=category&action=index');
                    exit;
                } else {
                    $errors['database'] = "Cập nhật danh mục thất bại.";
                }
            }

            $data = [
                'category' => $category,
                'errors' => $errors,
                'title' => 'Sửa Danh Mục'
            ];
            $this->view('/admin/categories/edit', $data);
        } else {
            $data = [
                'category' => $category,
                'title' => 'Sửa Danh Mục'
            ];
            $this->view('/admin/categories/edit', $data);
        }
    }

    public function delete($id) {
        $this->checkAdmin();

        $result = $this->categoryModel->deleteCategory($id);
        if ($result) {
            $_SESSION['success'] = "Xóa danh mục thành công.";
        } else {
            $_SESSION['error'] = "Xóa danh mục thất bại.";
        }
        header('Location: index.php?controller=category&action=index');
        exit;
    }

    public function deleted() {
        $deletedCategories = $this->categoryModel->getDeletedCategories();
        $data = [
            'categories' => $deletedCategories,
            'title' => 'Danh Mục Đã Xóa'
        ];
        $this->view('/admin/categories/deleted', $data);
    }


    public function restore($id) {
        $this->checkAdmin();
        if ($this->categoryModel->restoreCategory($id)) {
            $_SESSION['success'] = "Khôi phục danh mục thành công.";
        } else {
            $_SESSION['error'] = "Không thể khôi phục danh mục. Vui lòng thử lại.";
        }
        header('Location: index.php?controller=category&action=deleted');
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