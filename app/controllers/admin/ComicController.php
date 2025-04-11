<?php
require_once 'app/models/ComicModel.php';
require_once 'app/models/CategoryModel.php';

class ComicController
{
    private $comicModel;
    private $categoryModel;

    public function __construct($db)
    {
        $this->comicModel = new ComicModel($db);
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

    public function index()
    {
        $this->checkAdmin();

        $comics = $this->comicModel->getComics();
        if ($comics === false) {
            $_SESSION['error'] = "Không thể lấy danh sách sản phẩm. Vui lòng thử lại sau.";
            $comics = [];
        }

        $data = [
            'comics' => $comics,
            'title' => 'Quản Lý Comics'
        ];
        $this->view('/admin/products/index', $data);
    }

    public function add()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $author = isset($_POST['author']) ? trim($_POST['author']) : '';
            $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
            $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

            // Xử lý hình ảnh
            $image = '';
            $errors = [];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . '/../../../public/images/'; // Sửa đường dẫn
                // Kiểm tra và tạo thư mục nếu chưa tồn tại
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $imageName = time() . '_' . basename($_FILES['image']['name']); // Tạo tên file duy nhất
                $target_file = $target_dir . $imageName;

                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image = 'public/images/' . $imageName; // Đường dẫn lưu trong database
                    } else {
                        $errors['image'] = "Không thể upload hình ảnh.";
                    }
                } else {
                    $errors['image'] = "Chỉ cho phép upload file JPG, JPEG, PNG, GIF.";
                }
            } else {
                $errors['image'] = "Vui lòng chọn hình ảnh để upload.";
            }

            // Thêm sản phẩm vào cơ sở dữ liệu
            if (empty($title)) {
                $errors['title'] = "Tên sản phẩm không được để trống.";
            }
            if (empty($description)) {
                $errors['description'] = "Mô tả không được để trống.";
            }
            if (empty($author)) {
                $errors['author'] = "Tác giả không được để trống.";
            }
            if ($price <= 0) {
                $errors['price'] = "Giá sản phẩm phải lớn hơn 0.";
            }
            if ($category_id <= 0) {
                $errors['category_id'] = "Danh mục sản phẩm không hợp lệ.";
            }

            // Nếu không có lỗi, thêm sản phẩm
            if (empty($errors)) {
                $addResult = $this->comicModel->addComic($title, $description, $price, $author, $image, $category_id);

                if ($addResult === true) {
                    header('Location: index.php?controller=comic&action=index');
                    exit;
                } else {
                    $errors['database'] = "Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại.";
                }
            }

            // Nếu có lỗi, lấy danh sách danh mục để hiển thị
            $categories = $this->categoryModel->getCategories();
            $data = [
                'categories' => $categories,
                'errors' => $errors,
                'title' => 'Thêm Comic'
            ];
            $this->view('/admin/products/add', $data);
        } else {
            // Nếu không phải POST, hiển thị form thêm sản phẩm
            $categories = $this->categoryModel->getCategories();
            $data = [
                'categories' => $categories,
                'title' => 'Thêm Comic'
            ];
            $this->view('/admin/products/add', $data);
        }
    }

    public function edit($id)
    {
        $this->checkAdmin();
        // Kiểm tra xem sản phẩm có tồn tại không
        $comic = $this->comicModel->getComicById($id);
        if (!$comic) {
            // Nếu không tồn tại, có thể chuyển hướng đến trang không tìm thấy hoặc thông báo lỗi
            header('Location: index.php?controller=comic&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Làm sạch dữ liệu đầu vào
            $title = htmlspecialchars(trim($_POST['title']));
            $description = htmlspecialchars(trim($_POST['description']));
            $author = htmlspecialchars(trim($_POST['author']));
            $price = htmlspecialchars(trim($_POST['price']));

            $category_id = htmlspecialchars(trim($_POST['category_id']));
            $errors = [];

            // Giữ hình ảnh cũ nếu không upload mới
            $image = $comic->image;

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = __DIR__ . '/../../../public/images/'; // Sửa đường dẫn
                // Kiểm tra và tạo thư mục nếu chưa tồn tại
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $imageName = time() . '_' . basename($_FILES['image']['name']); // Tạo tên file duy nhất
                $target_file = $target_dir . $imageName;

                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image = 'public/images/' . $imageName; // Đường dẫn lưu trong database
                    } else {
                        $errors['image'] = "Không thể upload hình ảnh.";
                    }
                } else {
                    $errors['image'] = "Chỉ cho phép upload file JPG, JPEG, PNG, GIF.";
                }
            }

            if (empty($errors)) {
                $result = $this->comicModel->updateComic($id, $title, $description, $author, $price, $image, $category_id);

                if ($result === true) {
                    header('Location: index.php?controller=comic&action=index');
                    exit();
                } else {
                    $errors['update'] = "Cập nhật sản phẩm thất bại.";
                }
            }

            // Nếu có lỗi, chuẩn bị lại dữ liệu để hiển thị
            $categories = $this->categoryModel->getCategories();
            $data = [
                'comic' => $comic,
                'categories' => $categories,
                'errors' => $errors,
                'title' => 'Sửa Comic'
            ];
            $this->view('/admin/products/edit', $data);
        } else {
            $categories = $this->categoryModel->getCategories();
            $data = [
                'comic' => $comic,
                'categories' => $categories,
                'title' => 'Sửa Comic'
            ];
            $this->view('/admin/products/edit', $data);
        }
    }

    
   
    public function deleted()
    {
        $this->checkAdmin();
        $deletedComics = $this->comicModel->getDeletedComics();
        $data = [
            'comics' => $deletedComics,
            'title' => 'Comic Đã Xóa'
        ];
        $this->view('/admin/products/deleted', $data);
    }
   
    public function restore($id)
    {
        $this->checkAdmin();
        if ($this->comicModel->restoreComic($id)) {
            $_SESSION['success'] = "Khôi phục comic thành công.";
        } else {
            $_SESSION['error'] = "Không thể khôi phục comic. Vui lòng thử lại.";
        }
        header('Location: index.php?controller=comic&action=deleted');
        exit;
    }
    public function delete($id)
    {
        $this->checkAdmin();
        $this->comicModel->deleteComic($id);
        header('Location: index.php?controller=comic&action=index');
    }


    private function view($viewPath, $data = [])
    {
       
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