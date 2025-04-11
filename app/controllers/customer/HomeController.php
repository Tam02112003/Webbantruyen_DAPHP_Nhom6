<?php
require_once 'app/models/CategoryModel.php';
class HomeController {
    private $comicModel;
    private $categoryModel;
    public function __construct($comicModel, $categoryModel) {

        $this->comicModel = $comicModel;
        $this->categoryModel = $categoryModel;
    }

    // Hiển thị trang chính
    public function index() {
        // Lấy các tham số lọc từ URL
        $filters = [
            'price_range' => $_GET['price_range'] ?? null,
            'author' => $_GET['author'] ?? null,
            'category_id' => $_GET['category_id'] ?? null
        ];
        
        // Loại bỏ các tham số có giá trị 'all'
        $filters = array_filter($filters, function($value) {
            return $value !== 'all' && $value !== null;
        });
        
        // Lấy danh sách truyện theo bộ lọc
        $comics = $this->comicModel->getFilteredComics($filters);
        $categories = $this->categoryModel->getCategories();
        $authors = $this->comicModel->getUniqueAuthors();
        
        if ($comics === false) {
            $_SESSION['error'] = "Không thể lấy danh sách truyện. Vui lòng thử lại sau.";
            $comics = [];
        }
    
        $data = [
            'comics' => $comics,
            'categories' => $categories,
            'authors' => $authors,
            'current_filters' => $_GET, // Giữ nguyên các tham số GET để hiển thị UI
            'title' => 'Danh sách truyện',
        ];
        $this->view('/customer/home/index', $data);
    }

    public function search() {
        // Kiểm tra xem từ khóa có được gửi không
        if (isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];

            // Gọi hàm tìm kiếm từ model
            $comics = $this->comicModel->searchComics($keyword);

            // Truyền dữ liệu đến view
            $this->view('/customer/home/searchResults', [
                'comics' => $comics,
                'keyword' => $keyword
            ]);
        } else {
            // Nếu không có từ khóa, điều hướng về trang chính
            $_SESSION['error'] = "Vui lòng nhập từ khóa tìm kiếm.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    public function autocomplete() {
        if (isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            $comics = $this->comicModel->searchComics($keyword); // Kiểm tra phương thức này
    
             // Trả về mảng chứa các thông tin cần thiết
        $suggestions = array_map(function($comic) {
            return [
                'title' => $comic['title'],  
                'price' => $comic['price'], 
                'image' => $comic['image']    
            ];
        }, $comics);

    
            echo json_encode( $suggestions);
            exit;
        }
    }

    public function detail($id) {
        // Kiểm tra $id có hợp lệ không
        if (!is_numeric($id) || $id <= 0) {
            error_log("ID không hợp lệ trong HomeController::detail: " . $id);
            $_SESSION['error'] = "Sản phẩm không hợp lệ.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    
        $comic = $this->comicModel->getComicById($id);
        if ($comic === false) {
            error_log("Không thể lấy thông tin sản phẩm với ID: $id");
            $_SESSION['error'] = "Không thể lấy thông tin sản phẩm. Vui lòng thử lại sau.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        if (!$comic) {
            $_SESSION['error'] = "Sản phẩm không tồn tại.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        $data = [
            'comic' => $comic,
            'title' => 'Chi Tiết Sản Phẩm: ' . htmlspecialchars($comic->title),
        ];
        $this->view('/customer/home/detail', $data);
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
        $layoutFile = 'app/views/layouts/customer_layout.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            die("Lỗi: Không tìm thấy tệp layout: " . $layoutFile);
        }
    }
}
?>