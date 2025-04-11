<?php

class CartModel {
    private $conn;
    private $table_name = "cart_items";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addItem($user_id, $comic_id, $quantity, $price) {
        // Kiểm tra xem user_id có phải là NULL không
        if ($user_id === null) {
            throw new Exception("User ID cannot be null");
        }
    
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id AND comic_id = :comic_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':comic_id', $comic_id);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            // Nếu sản phẩm đã có, cập nhật số lượng
            $query = "UPDATE " . $this->table_name . " SET quantity = quantity + :quantity WHERE user_id = :user_id AND comic_id = :comic_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':comic_id', $comic_id);
            return $stmt->execute();
        } else {
            // Nếu sản phẩm chưa có, thêm mới
            $query = "INSERT INTO " . $this->table_name . " (user_id, comic_id, quantity, price) 
                      VALUES (:user_id, :comic_id, :quantity, :price)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':comic_id', $comic_id);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':price', $price);
            return $stmt->execute();
        }
    }

    public function getCartItemsByUserId($user_id) {
        // Lấy comic_id từ cart_items
        $query = "SELECT comic_id, quantity FROM cart_items WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $cartItems = $stmt->fetchAll(PDO::FETCH_OBJ);
    
        // Lấy thông tin chi tiết về từng truyện
        $detailedItems = [];
        foreach ($cartItems as $item) {
            $comicQuery = "SELECT id, title, price FROM comics WHERE id = :comic_id"; // Giả sử bạn có bảng comics
            $comicStmt = $this->conn->prepare($comicQuery);
            $comicStmt->bindParam(':comic_id', $item->comic_id);
            $comicStmt->execute();
            $comicDetail = $comicStmt->fetch(PDO::FETCH_OBJ);
    
            // Kết hợp thông tin
            if ($comicDetail) {
                $detailedItems[] = (object) [
                    'comic_id' => $comicDetail->id,
                    'comic_name' => $comicDetail->title,
                    'price' => $comicDetail->price,
                    'quantity' => $item->quantity
                ];
            }
        }
        return $detailedItems;
    }
    // Xóa sản phẩm khỏi giỏ hàng
    public function removeItem($user_id, $comic_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id AND comic_id = :comic_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':comic_id', $comic_id);
        return $stmt->execute();
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateItem($user_id, $comic_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($user_id, $comic_id);
        }
        
        $query = "UPDATE " . $this->table_name . " SET quantity = :quantity WHERE user_id = :user_id AND comic_id = :comic_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':comic_id', $comic_id);
        return $stmt->execute();
    }

    public function getItems($user_id) {
        
       $query = "SELECT cart.comic_id, cart.quantity, cart.price, comics.title ,comics.image
                  FROM " . $this->table_name . " cart 
                  JOIN comics ON cart.comic_id = comics.id 
                  WHERE cart.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ); // Giả sử bạn muốn trả về một mảng đối tượng
    }

    // Tính tổng giá trị giỏ hàng
    public function getTotal($user_id) {
        $query = "SELECT SUM(quantity * price) AS total FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    // Xóa tất cả sản phẩm trong giỏ hàng
    public function clear($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
    function formatCurrency($amount) {
        return htmlspecialchars(number_format($amount, 2)) . ' VNĐ';
    }

}
?>