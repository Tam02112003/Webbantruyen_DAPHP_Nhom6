<?php
class OrderModel {
    private $conn;
    private $table_name = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function beginTransaction() {
        $this->conn->beginTransaction();
    }

    public function commit() {
        $this->conn->commit();
    }

    public function rollBack() {
        $this->conn->rollBack();
    }

    public function createOrder($user_id,$subtotal, $shipping_fee, $total, $name, $address, $phone, $email) {
        $query = "INSERT INTO " . $this->table_name . " (user_id,subtotal, 
                    shipping_fee,  total, order_date, name, address, phone, email, status) 
                  VALUES (:user_id, :subtotal, :shipping_fee,  :total, NOW(), :name, :address, :phone, :email, 'Đang chờ xác nhận')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':subtotal', $subtotal);
        $stmt->bindParam(':shipping_fee', $shipping_fee);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function updateStatus($order_id, $status) {
        $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $order_id);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Trả về true nếu có hàng được cập nhật
        }
        return false; // Trả về false nếu có lỗi
    }

    public function addOrderDetail($order_id, $comic_id, $quantity, $price) {
        $query = "INSERT INTO order_items (order_id, comic_id, quantity, price) 
                  VALUES (:order_id, :comic_id, :quantity, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':comic_id', $comic_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        return $stmt->execute();
    }

    // Lấy thông tin đơn hàng theo order_id
    public function getOrderById($order_id) {
        $query = "SELECT o.*, u.username, u.email, o.status 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.user_id = u.user_id 
                  WHERE o.order_id = :order_id"; 
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getOrderDetails($order_id) {
        $query = "SELECT oi.*, c.title
                  FROM order_items oi 
                  JOIN comics c ON oi.comic_id = c.id 
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy lịch sử đơn hàng của người dùng
    public function getOrdersByUserId($user_id) {
        $query = "SELECT o.*, u.username, u.email, o.status 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.user_id = u.user_id 
                  WHERE o.user_id = :user_id 
                  ORDER BY o.order_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllOrders() {
        $query = "SELECT o.*, u.username, u.email 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.user_id = u.user_id
                  ORDER BY o.order_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>