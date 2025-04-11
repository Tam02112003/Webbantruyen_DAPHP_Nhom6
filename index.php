<?php
session_start();
require_once 'app/config/database.php';
require_once 'app/controllers/admin/ComicController.php';
require_once 'app/controllers/admin/CategoryController.php';
require_once 'app/controllers/customer/HomeController.php'; // Thêm HomeController
require_once 'app/controllers/customer/CartController.php'; // Thêm CartController
require_once 'app/controllers/auth/AuthController.php'; // Thêm AuthController
require_once 'app/controllers/customer/OrderController.php';
require_once 'app/controllers/admin/AdminOrderController.php';
require_once 'app/controllers/admin/AdminController.php'; // Thêm AdminController
require_once 'app/controllers/admin/UserController.php'; // Thêm UserController
require_once 'app/models/UserModel.php'; // Thêm UserModel
require_once 'app/models/CartModel.php'; // Giả sử bạn đã tạo model CartModel
require_once 'app/models/ComicModel.php'; // Bao gồm model ComicModel
require_once 'app/models/CategoryModel.php'; // Bao gồm model CategoryModel
require_once 'app/models/OrderModel.php'; // Bao gồm model OrderModel

$db = new Database();
$conn = $db->getConnection();

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home'; // Mặc định về controller home
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Xác thực controller và action
$validControllers = ['comic', 'category', 'home', 'cart', 'auth', 'order', 'user', 'admin'];
$validActions = [
    'index',
    'add',
    'edit',
    'delete',
    'deleted',
    'restore',
    'update',
    'remove',
    'clear',
    'register',
    'login',
    'logout',
    'customerInfo',
    'showRegisterForm',
    'showLoginForm',
    'checkout',
    'success',
    'history',
    'orderDetail',
    'show',
    'updateRole',
    'detail',
    'search',
    'autocomplete',
    'updateStatus'
];

if (!in_array($controller, $validControllers)) {
    $controller = 'home';
}

if (!in_array($action, $validActions)) {
    $action = 'index';
}

// Tạo controller và gọi action
switch ($controller) {
    case 'comic':
        $comicController = new ComicController($conn);
        break;
    case 'category':
        $categoryController = new CategoryController($conn);
        break;
    case 'home':
        $comicModel = new ComicModel($conn);
        $categoryModel = new CategoryModel($conn);
        $homeController = new HomeController($comicModel, $categoryModel);
        break;
    case 'cart':
        $cartController = new CartController($conn);
        break;
    case 'auth':
        $authController = new AuthController(new UserModel($conn));
        break;
    case 'order':
        $orderController = new OrderController($conn);
        break;
    case 'user':
        $userController = new UserController($conn);
        break;
    case 'admin':
        $adminController = new AdminController();
        break;

}

if (isset($comicController)) {
    switch ($action) {
        case 'index':
            $comicController->index();
            break;
        case 'add':
            $comicController->add();
            break;
        case 'edit':
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            if ($id) {
                $comicController->edit($id);
            } else {
                echo "ID không hợp lệ.";
            }
            break;
        case 'delete':
            $id = $_GET['id'] ?? 0;
            $comicController->delete($id);
            break;

        case 'deleted':
            $comicController->deleted();
            break;
        case 'restore':
            $id = $_GET['id'] ?? 0;
            $comicController->restore($id);
            break;
        default:
            $comicController->index();
            break;
    }
} elseif (isset($categoryController)) {
    switch ($action) {
        case 'index':
            $categoryController->index();
            break;
        case 'add':
            $categoryController->add();
            break;
        case 'edit':
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            if ($id) {
                $categoryController->edit($id);
            } else {
                echo "ID không hợp lệ.";
            }
            break;
        case 'delete':
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            if ($id) {
                $categoryController->delete($id);
            } else {
                echo "ID không hợp lệ.";
            }
            break;

        case 'deleted':
            $categoryController->deleted();
            break;
        case 'restore':
            $id = $_GET['id'] ?? 0;
            $categoryController->restore($id);
            break;
        default:
            $categoryController->index();
            break;
    }
} elseif (isset($homeController)) {
    switch ($action) {
        case 'index':
            $homeController->index();
            break;
        case 'detail':
            $comic_id = $_GET['id'] ?? 0;
            $homeController->detail($comic_id);
            break;
        case 'autocomplete':
            $homeController->autocomplete();
            break;
        case 'search':
            $homeController->search();
            break;
        default:
            $homeController->index();
            break;
    }
} elseif (isset($cartController)) {
    switch ($action) {
        case 'index':
            $cartController->index();
            break;
        case 'add':
            $cartController->add();
            break;
        case 'update':
            $cartController->update();
            break;
        case 'remove':
            $cartController->remove();
            break;
        case 'clear':
            $cartController->clear();
            break;
        default:
            $cartController->index();
            break;
    }
} elseif (isset($authController)) {
    switch ($action) {
        case 'showRegisterForm':
            $authController->showRegisterForm(); // Hiển thị form đăng ký
            break;
        case 'register':
            $authController->register(); // Xử lý đăng ký
            break;
        case 'showLoginForm':
            $authController->showLoginForm(); // Hiển thị form đăng nhập
            break;
        case 'login':
            $authController->login(); // Xử lý đăng nhập
            break;
        case 'logout':
            $authController->logout(); // Xử lý đăng xuất
            break;
        default:
            $authController->showLoginForm(); // Mặc định gọi trang đăng nhập
            break;
    }
} elseif (isset($cartController)) {
    switch ($action) {
        case 'index':
            $cartController->index(); // Hiển thị giỏ hàng
            break;
        case 'add':
            $cartController->add(); // Thêm sản phẩm vào giỏ hàng
            break;
        case 'update':
            $cartController->update(); // Cập nhật số lượng sản phẩm trong giỏ hàng
            break;
        case 'remove':
            $cartController->remove(); // Xóa sản phẩm khỏi giỏ hàng
            break;
        case 'clear':
            $cartController->clear(); // Xóa toàn bộ giỏ hàng
            break;
        default:
            $cartController->index(); // Mặc định gọi index
            break;
    }
} elseif ($controller === 'order') {
    // Kiểm tra xem đây là OrderController của admin hay customer
    if (isset($_GET['action']) && in_array($_GET['action'], ['index', 'show', 'updateStatus'])) {
        // OrderController cho admin
        $orderController = new AdminOrderController($conn); // Sử dụng AdminOrderController
        switch ($action) {
            case 'index':
                $orderController->index();
                break;
            case 'show':
                $order_id = $_GET['order_id'] ?? 0;
                $orderController->show($order_id);
                break;
                case 'updateStatus':
                    $orderController->updateStatus(); // Gọi phương thức không cần tham số
                    break;
            default:
                $orderController->index();
                break;
        }
    } else {
        // OrderController cho customer
        $orderController = new OrderController($conn); // Sử dụng OrderController
        switch ($action) {

            case 'customerInfo':
                $orderController->customerInfo();
                break;
            case 'checkout':
                $orderController->checkout();
                break;
            case 'success':
                $orderController->success();
                break;
            case 'history':
                $orderController->history();
                break;
            case 'orderDetail':
                $orderController->orderDetail();
                break;
            default:
                $orderController->customerInfo();
                break;
        }
    }

} elseif ($controller === 'user') {
    switch ($action) {
        case 'index':
            $userController->index();
            break;
        case 'updateRole':
            $userId = $_GET['user_id'] ?? 0;
            $userController->updateRole($userId);
            break;
        default:
            $userController->index();
            break;
    }
} elseif ($controller === 'admin') {
    switch ($action) {
        case 'index':
            $adminController->index();
            break;
        default:
            $adminController->index();
            break;
    }
}
?>