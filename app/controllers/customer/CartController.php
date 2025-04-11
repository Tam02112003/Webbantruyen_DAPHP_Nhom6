<?php
class CartController
{
    private $cartModel;
    private $comicModel;

    public function __construct()
    {
        $database = new Database();
        $this->cartModel = new CartModel($database->getConnection());
        $this->comicModel = new ComicModel($database->getConnection());
    }

    // Hiển thị giỏ hàng
    public function index()
    {
        $this->ensureUserIsLoggedIn();

        $user_id = $_SESSION['user']['user_id'];
        $cartItems = $this->cartModel->getItems($user_id);
        $total = $this->cartModel->getTotal($user_id);

        $this->view('/customer/cart/index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'title' => 'Giỏ hàng',
        ]);
    }

    // Kiểm tra xem người dùng đã đăng nhập chưa
    private function ensureUserIsLoggedIn()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
            $_SESSION['error'] = "Bạn cần đăng nhập để thực hiện hành động này.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }
    }
    
    public function add() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) { // Sửa 'id' thành 'user_id'
            $_SESSION['error'] = "Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }

        $user_id = $_SESSION['user']['user_id']; // Sửa 'id' thành 'user_id'

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comic_id = isset($_POST['comic_id']) ? intval($_POST['comic_id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

            if ($comic_id > 0 && $quantity > 0) {
                $price = $this->comicModel->getPrice($comic_id);
                if ($price >= 0) {
                    $this->cartModel->addItem($user_id, $comic_id, $quantity, $price);
                    header('Location: index.php?controller=cart&action=index');
                    exit;
                } else {
                    $_SESSION['error'] = "Không thể lấy giá sản phẩm.";
                }
            } else {
                $_SESSION['error'] = "Sản phẩm không hợp lệ.";
            }
        }
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function remove()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) { // Sửa 'id' thành 'user_id'
            $_SESSION['error'] = "Bạn cần đăng nhập để xóa sản phẩm.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }

        $user_id = $_SESSION['user']['user_id']; // Sửa 'id' thành 'user_id'
        $comic_id = isset($_GET['comic_id']) ? intval($_GET['comic_id']) : 0;

        if ($comic_id > 0) {
            $this->cartModel->removeItem($user_id, $comic_id);
            header('Location: index.php?controller=cart&action=index');
            exit;
        }
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function update()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) { // Sửa 'id' thành 'user_id'
            $_SESSION['error'] = "Bạn cần đăng nhập để cập nhật giỏ hàng.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }

        $user_id = $_SESSION['user']['user_id']; // Sửa 'id' thành 'user_id'

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comic_id = isset($_POST['comic_id']) ? intval($_POST['comic_id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

            if ($comic_id > 0) {
                $this->cartModel->updateItem($user_id, $comic_id, $quantity);
                header('Location: index.php?controller=cart&action=index');
                exit;
            }
        }
    }

    // Xóa tất cả sản phẩm trong giỏ hàng
    public function clear()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) { // Sửa 'id' thành 'user_id'
            $_SESSION['error'] = "Bạn cần đăng nhập để xóa giỏ hàng.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }

        $user_id = $_SESSION['user']['user_id']; // Sửa 'id' thành 'user_id'
        $this->cartModel->clear($user_id);
        header('Location: index.php?controller=cart&action=index');
        exit;
    }
        // Phương thức để hiển thị view
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