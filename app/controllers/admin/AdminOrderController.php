<?php

class AdminOrderController {
    private $orderModel;

    public function __construct($db) {
        $this->orderModel = new OrderModel($db);
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



    // Hiển thị danh sách tất cả đơn hàng
    public function index() {
        $this->checkAdmin();

        try {
            $orders = $this->orderModel->getAllOrders();
            if ($orders === false) {
                throw new Exception("Không thể lấy danh sách đơn hàng.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() . " Vui lòng thử lại sau.";
            $orders = [];
        }

        $data = [
            'orders' => $orders,
            'title' => 'Quản Lý Đơn Hàng'
        ];
        $this->view('/admin/orders/index', $data);
    }

    // Hiển thị chi tiết một đơn hàng (đổi tên từ view() thành show())
    public function show($order_id) {
        $this->checkAdmin();

        // Kiểm tra $order_id hợp lệ
        if (!is_numeric($order_id) || $order_id <= 0) {
            $_SESSION['error'] = "ID đơn hàng không hợp lệ.";
            header('Location: index.php?controller=order&action=index');
            exit;
        }

        try {
            $order = $this->orderModel->getOrderById($order_id);
            if (!$order) {
                throw new Exception("Đơn hàng không tồn tại.");
            }

            $order_details = $this->orderModel->getOrderDetails($order_id);
            if ($order_details === false) {
                throw new Exception("Không thể lấy chi tiết đơn hàng.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() . " Vui lòng thử lại sau.";
            header('Location: index.php?controller=order&action=index');
            exit;
        }

        $data = [
            'order' => $order,
            'order_details' => $order_details,
            'title' => 'Chi Tiết Đơn Hàng #' . $order_id
        ];
        $this->view('/admin/orders/view', $data);
    }

    public function updateStatus() {
        $this->checkAdmin();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ POST
            $order_id = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;
    
            // Kiểm tra giá trị đầu vào
            $order_id = filter_var($order_id, FILTER_VALIDATE_INT);
            $status = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
    
            // Kiểm tra trạng thái hợp lệ
            $validStatuses = ['Đang chờ xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'];
            
            if ($order_id && $status && in_array($status, $validStatuses)) {
                if ($this->orderModel->updateStatus($order_id, $status)) {
                    $_SESSION['success'] = "Trạng thái đơn hàng đã được cập nhật thành công.";
                } else {
                    $_SESSION['error'] = "Cập nhật trạng thái đơn hàng không thành công.";
                }
            } else {
                error_log("Thông tin không hợp lệ: Order ID: $order_id, Status: $status");
                $_SESSION['error'] = "Thông tin cập nhật không hợp lệ.";
            }
        } else {
            $_SESSION['error'] = "Yêu cầu không hợp lệ.";
        }
    
        // Chuyển hướng về trang danh sách đơn hàng
        header('Location: index.php?controller=order&action=index');
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