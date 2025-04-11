<?php
class CategoryModel
{
    private $conn;
    private $table_name = "categories";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCategories()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCategoryById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function categoryExists($name, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE name = :name";
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(':name', $name);
        if ($excludeId !== null) {
            $stmt->bindParam(':excludeId', $excludeId);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    public function addCategory($name)
    {
        if ($this->categoryExists($name)) {
            return ['error' => 'Tên danh mục "' . htmlspecialchars($name) . '" đã tồn tại. Vui lòng chọn tên khác.'];
        }
        $query = "INSERT INTO " . $this->table_name . "(name, is_deleted) VALUES (:name, 0)";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateCategory($id, $name) {
        // Kiểm tra xem danh mục có tồn tại và chưa bị xóa mềm không
        $category = $this->getCategoryById($id);
        if (!$category) {
            return ['error' => 'Danh mục không tồn tại hoặc đã bị xóa.'];
        }

        // Kiểm tra trùng lặp tên danh mục (trừ bản ghi hiện tại)
        if ($this->categoryExists($name, $id)) {
            return ['error' => 'Tên danh mục "' . htmlspecialchars($name) . '" đã tồn tại. Vui lòng chọn tên khác.'];
        }

        // Cập nhật danh mục
        $query = "UPDATE " . $this->table_name . " SET name = :name WHERE id = :id AND is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0 ? true : ['error' => 'Không có thay đổi nào được thực hiện.'];
        }
        return ['error' => 'Không thể cập nhật danh mục. Vui lòng thử lại.'];
    }

    // Phương thức xóa mềm danh mục
    public function deleteCategory($id)
    {
        $query = "UPDATE " . $this->table_name . " SET is_deleted = 1 WHERE id = :id AND is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Trả về true nếu có bản ghi được cập nhật
        }
        return false;
    }
    // Lấy danh sách danh mục đã xóa mềm
    public function getDeletedCategories()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_deleted = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Khôi phục danh mục đã xóa mềm
    public function restoreCategory($id)
    {
        $query = "UPDATE " . $this->table_name . " SET is_deleted = 0 WHERE id = :id AND is_deleted = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Trả về true nếu có bản ghi được cập nhật
        }
        return false;
    }
}
?>