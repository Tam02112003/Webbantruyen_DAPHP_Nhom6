<?php
class OrderController {
    private $orderModel;
    private $cartModel;


    
    public function __construct($conn) {
        $this->orderModel = new OrderModel($conn);
        $this->cartModel = new CartModel($conn);
    }

    private function calculateShippingFee($subtotal) {
        // Dưới 100,000đ: phí ship 30,000đ
        // Trên hoặc bằng 100,000đ: Freeship
        return ($subtotal < 100000) ? 30000 : 0;
    }

    // Hiển thị lịch sử đơn hàng
    public function history() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) { 
            $_SESSION['error'] = "Bạn cần đăng nhập để xem lịch sử đơn hàng.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }

        $user_id = $_SESSION['user']['user_id']; // Sửa 'id' thành 'user_id'
        $orders = $this->orderModel->getOrdersByUserId($user_id);

        $data = [
            'orders' => $orders,
            'title' => 'Lịch sử đơn hàng',
        ];
        $this->view('/customer/order/history', $data);
    }

    // Hiển thị chi tiết đơn hàng
    public function orderDetail() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) { 
            $_SESSION['error'] = "Bạn cần đăng nhập để xem chi tiết đơn hàng.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }

        $user_id = $_SESSION['user']['user_id']; // Sửa 'id' thành 'user_id'
        $order_id = $_GET['order_id'] ?? 0;

        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderById($order_id);
        if (!$order || $order->user_id != $user_id) {
            $_SESSION['error'] = "Đơn hàng không tồn tại hoặc bạn không có quyền xem.";
            header('Location: index.php?controller=order&action=history');
            exit;
        }

        // Lấy chi tiết đơn hàng
        $orderDetails = $this->orderModel->getOrderDetails($order_id);

        $data = [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'title' => 'Chi tiết đơn hàng',
        ];
        $this->view('/customer/order/orderDetail', $data);
    }

    public function customerInfo() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
            $_SESSION['error'] = "Bạn cần đăng nhập để thanh toán.";
            header('Location: index.php?controller=auth&action=showLoginForm');
            exit;
        }
    
        // Lấy user_id từ session
        $user_id = $_SESSION['user']['user_id'];
        $email = $_SESSION['user']['email'];
        // Lấy thông tin giỏ hàng của người dùng
        $cartItems = $this->cartModel->getCartItemsByUserId($user_id);
        
        // Tính toán
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->quantity * $item->price;
        }
        
        // Tính phí ship (QUAN TRỌNG: chỉ tính 1 lần)
        $shippingFee = $this->calculateShippingFee($subtotal);
        $total = $subtotal + $shippingFee; // Tổng FINAL = subtotal + phí ship
    
        // Truyền dữ liệu đến view
        $this->view('/customer/order/customerInfo', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal, // Chỉ tiền hàng
            'shipping_fee' => $shippingFee, // Phí ship riêng
            'total' => $total, // Tổng cuối cùng
            'email' => $email,
            'title' => 'Thông tin thanh toán'
        ]);
    }

    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) { // Sửa 'id' thành 'user_id'
                $_SESSION['error'] = "Bạn cần đăng nhập để thanh toán.";
                header('Location: index.php?controller=auth&action=showLoginForm');
                exit;
            }
    
            $user_id = $_SESSION['user']['user_id']; // Sửa 'id' thành 'user_id'
            $name = $_POST['name'] ?? '';
            $address = $_POST['address'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $email = $_POST['email'] ?? '';
    
            if (empty($name) || empty($address) || empty($phone) || empty($email)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin.";
                header('Location: index.php?controller=order&action=customerInfo');
                exit;
            }
    
            $cartItems = $this->cartModel->getItems($user_id);
             // Tính toán lại tổng giá và phí ship
             $subtotal = 0;
             foreach ($cartItems as $item) {
                 $subtotal += $item->quantity * $item->price;
             }
             $shippingFee = $this->calculateShippingFee($subtotal);
             $total = $subtotal + $shippingFee;

    
            if (empty($cartItems)) {
                $_SESSION['error'] = "Giỏ hàng của bạn đang trống.";
                header('Location: index.php?controller=cart&action=index');
                exit;
            }
    
            $this->orderModel->beginTransaction();
    
            try {
                $order_id = $this->orderModel->createOrder($user_id, $subtotal,
                $shippingFee, $total, $name, $address, $phone, $email);
                if (!$order_id) {
                    throw new Exception("Không thể tạo đơn hàng.");
                }
    
                foreach ($cartItems as $item) {
                    $success = $this->orderModel->addOrderDetail(
                        $order_id,
                        $item->comic_id,
                        $item->quantity,
                        $item->price
                    );
                    if (!$success) {
                        throw new Exception("Không thể thêm chi tiết đơn hàng.");
                    }
                }
    
                $this->cartModel->clear($user_id);
                $this->orderModel->commit();
    
                // Lưu order_id vào session trước khi chuyển hướng
                $_SESSION['order_id'] = $order_id;
                $_SESSION['success'] = "Đặt hàng thành công! Đơn hàng của bạn đã được tạo.";
                if (ob_get_length()) {
                    ob_end_clean();
                }
                header('Location: index.php?controller=order&action=success');
                exit;
            } catch (Exception $e) {
                $this->orderModel->rollBack();
                $_SESSION['error'] = "Thanh toán thất bại: " . $e->getMessage();
                header('Location: index.php?controller=cart&action=index');
                exit;
            }
        } else {
            $this->customerInfo();
        }
    }

    public function success() {
        if (!isset($_SESSION['order_id'])) {
            $_SESSION['error'] = "Không tìm thấy thông tin đơn hàng.";
            header('Location: index.php?controller=cart&action=index');
            exit;
        }
    
        $order_id = $_SESSION['order_id'];
        $order = $this->orderModel->getOrderById($order_id);
        $orderDetails = $this->orderModel->getOrderDetails($order_id);
    
        $data = [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'title' => 'Thanh toán thành công'
        ];
        $this->view('/customer/order/success', $data);
        unset($_SESSION['order_id']);
    }

    private function view($viewPath, $data = []) {
        // Bắt đầu bộ đệm đầu ra để lấy nội dung view
        ob_start();
        extract($data);
        $file = 'app/views' . $viewPath . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            die("Lỗi: Không tìm thấy tệp view: " . $file);
        }
        $content = ob_get_clean();

        // Truyền nội dung view và các dữ liệu khác vào layout
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