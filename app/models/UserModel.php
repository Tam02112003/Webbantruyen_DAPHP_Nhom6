<?php
class UserModel
{
    private $conn;
    private $table_name = "users"; // Tên bảng người dùng

    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Lấy thông tin người dùng theo ID
    public function getUserById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Lấy tất cả người dùng
    public function getAllUsers() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đăng ký người dùng mới
    public function register($username, $email, $password = '') {
        // Kiểm tra xem người dùng đã tồn tại hay chưa
        if ($this->userExists($email)) {
            return false;
        }
        
        // Kiểm tra xem username đã tồn tại hay chưa
        if ($this->usernameExists($username)) {
            // Nếu username đã tồn tại, thêm số ngẫu nhiên vào cuối
            $i = 1;
            $originalUsername = $username;
            while ($this->usernameExists($username)) {
                $username = $originalUsername . '_' . $i;
                $i++;
                if ($i > 10) break; // Giới hạn số lần thử
            }
        }
    
        // Mã hóa mật khẩu nếu có
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
        } else {
            // Nếu không có mật khẩu, có thể đặt một mật khẩu ngẫu nhiên (hoặc xử lý theo cách khác)
            $password = password_hash(uniqid(), PASSWORD_BCRYPT); // Tạo mật khẩu ngẫu nhiên
        }
        
        // Thực hiện truy vấn để thêm người dùng vào cơ sở dữ liệu
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
    
        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Lỗi khi thêm người dùng: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    }

    public function login($username, $password) {
        $query = "SELECT user_id, username, email, password, role FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $username = htmlspecialchars(strip_tags($username));
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra dữ liệu trả về
        if (!$user) {
            error_log("Không tìm thấy người dùng với username: " . $username);
            return false;
        }

        if (!isset($user['role'])) {
            error_log("Cột role không tồn tại trong kết quả truy vấn cho username: " . $username);
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // Kiểm tra vai trò của người dùng
    public function isAdmin($userId)
    {
        $user = $this->getUserById($userId);
        return $user && $user->role === 'admin';
    }

    public function usernameExists($username)
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Trả về true nếu tên người dùng đã tồn tại
    }

    // Kiểm tra người dùng đã tồn tại chưa
    public function userExists($email) {
        // Thực hiện truy vấn để kiểm tra
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    public function logout()
    {
        // Xóa toàn bộ session của người dùng
        session_unset();
        session_destroy();

        // Thiết lập thông báo đăng xuất thành công
        $_SESSION['success'] = "Đăng xuất thành công!";

        // Chuyển hướng về trang đăng nhập hoặc trang chủ
        header('Location: index.php?controller=auth&action=showLoginForm');
        exit;
    }

    // Cập nhật vai trò của người dùng
    public function updateRole($userId, $role) {
        if (!in_array($role, ['user', 'admin'])) {
            return ['error' => 'Vai trò không hợp lệ.'];
        }

        $query = "UPDATE " . $this->table_name . " SET role = :role WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $userId);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0 ? true : ['error' => 'Không có thay đổi nào được thực hiện.'];
        }
        return ['error' => 'Không thể cập nhật vai trò. Vui lòng thử lại.'];
    }
    public function updateUser($email) {
        // Cập nhật thời gian đăng nhập cuối cùng
        $query = "UPDATE users SET last_login = NOW() WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function getUserIdByEmail($email) {
        $stmt = $this->conn->prepare("SELECT user_id, role FROM users WHERE email = ?"); // Thay 'user_id' bằng tên cột thực tế
        $stmt->execute([$email]);
        return $stmt->fetchColumn(); // Trả về ID người dùng
    }

    public function getUserByEmail($email) {
        $query = "SELECT user_id, username, email, role FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function generateUsernameFromEmail($email, $addRandom = false) {
        $baseUsername = strtok($email, '@'); // Lấy phần trước @
        $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', $baseUsername); // Loại bỏ ký tự đặc biệt
        
        if ($addRandom) {
            $baseUsername .= '_' . bin2hex(random_bytes(2)); // Thêm chuỗi ngẫu nhiên
        }
        
        return $baseUsername;
    }
}
?>