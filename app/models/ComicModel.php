<?php
class ComicModel {
    private $conn;
    private $table_name = "comics";

    public function __construct($db) {
        if (!$db instanceof PDO) {
            error_log("Đối tượng PDO không hợp lệ được truyền vào ComicModel.");
            throw new Exception("Đối tượng PDO không hợp lệ được truyền vào ComicModel.");
        }
        $this->conn = $db;
    }

    public function getComics() {
        try {
            $query = "SELECT p.id, p.title, p.author, p.description, p.price, p.image, p.created_at, c.name AS category_name
                      FROM " . $this->table_name . " p
                      LEFT JOIN categories c ON p.category_id = c.id
                      WHERE p.is_deleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Lỗi trong getComics: " . $e->getMessage());
            return false;
        }
    }

    public function getComicById($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                error_log("ID không hợp lệ trong getComicById: " . $id);
                return false;
            }
    
            $query = "SELECT p.*, c.name AS category_name 
            FROM " . $this->table_name . " p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :id AND p.is_deleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($result === false) {
                error_log("Không tìm thấy sản phẩm với ID: " . $id);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Lỗi trong getComicById: " . $e->getMessage());
            return false;
        }
    }

    public function addComic($title, $description, $price, $author, $image, $category_id) {
        $errors = [];

        // Kiểm tra dữ liệu đầu vào
        if (empty($title)) {
            $errors['title'] = 'Tên sản phẩm không được để trống';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        if (empty($author)) {
            $errors['author'] = 'Tác giả không được để trống';
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }
        if (!is_numeric($category_id) || $category_id <= 0) {
            $errors['category_id'] = 'Danh mục sản phẩm không hợp lệ';
        }

        // Nếu có lỗi, trả về mảng lỗi
        if (count($errors) > 0) {
            return $errors;
        }

        try {
            // Câu lệnh SQL để thêm sản phẩm
            $query = "INSERT INTO " . $this->table_name . " (title, description, price, author, image, category_id, created_at, is_deleted) 
                      VALUES (:title, :description, :price, :author, :image, :category_id, CURRENT_TIMESTAMP, 0)";
            $stmt = $this->conn->prepare($query);
            
            // Làm sạch dữ liệu đầu vào
            $title = htmlspecialchars(strip_tags($title));
            $description = htmlspecialchars(strip_tags($description));
            $author = htmlspecialchars(strip_tags($author));
            $price = floatval($price);
            $image = htmlspecialchars(strip_tags($image));
            $category_id = intval($category_id);
        
            // Liên kết tham số
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        
            // Thực thi câu lệnh
            if ($stmt->execute()) {
                return true;
            }
            error_log("Không thể thêm sản phẩm: " . $stmt->errorInfo()[2]);
            return false;
        } catch (PDOException $e) {
            error_log("Lỗi trong addComic: " . $e->getMessage());
            return false;
        }
    }

    public function updateComic($id, $title, $description, $author, $price, $image, $category_id) {
        try {
            // Kiểm tra dữ liệu đầu vào
            if (!is_numeric($id) || $id <= 0) {
                error_log("ID không hợp lệ trong updateComic: " . $id);
                return false;
            }
            if (empty($title)) {
                error_log("Tên sản phẩm không được để trống trong updateComic");
                return false;
            }
            if (empty($description)) {
                error_log("Mô tả không được để trống trong updateComic");
                return false;
            }
            if (empty($author)) {
                error_log("Tác giả không được để trống trong updateComic");
                return false;
            }
            if (!is_numeric($price) || $price <= 0) {
                error_log("Giá sản phẩm không hợp lệ trong updateComic: " . $price);
                return false;
            }
            if (!is_numeric($category_id) || $category_id <= 0) {
                error_log("Danh mục sản phẩm không hợp lệ trong updateComic: " . $category_id);
                return false;
            }

            $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description, author = :author, price = :price, image = :image, category_id = :category_id WHERE id = :id AND is_deleted = 0";
            $stmt = $this->conn->prepare($query);

            // Làm sạch dữ liệu đầu vào
            $title = htmlspecialchars(strip_tags($title));
            $description = htmlspecialchars(strip_tags($description));
            $author = htmlspecialchars(strip_tags($author));
            $price = floatval($price);
            $image = htmlspecialchars(strip_tags($image));
            $category_id = intval($category_id);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            error_log("Không thể cập nhật sản phẩm: " . $stmt->errorInfo()[2]);
            return false;
        } catch (PDOException $e) {
            error_log("Lỗi trong updateComic: " . $e->getMessage());
            return false;
        }
    }

    public function deleteComic($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                error_log("ID không hợp lệ trong deleteComic: " . $id);
                return false;
            }

            $query = "UPDATE " . $this->table_name . " SET is_deleted = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            error_log("Không thể xóa mềm sản phẩm: " . $stmt->errorInfo()[2]);
            return false;
        } catch (PDOException $e) {
            error_log("Lỗi trong deleteComic: " . $e->getMessage());
            return false;
        }
    }

    public function getPrice($comic_id) {
        try {
            if (!is_numeric($comic_id) || $comic_id <= 0) {
                error_log("ID không hợp lệ trong getPrice: " . $comic_id);
                return -1;
            }

            $query = "SELECT price FROM " . $this->table_name . " WHERE id = :comic_id AND is_deleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comic_id', $comic_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['price'] : -1;
        } catch (PDOException $e) {
            error_log("Lỗi trong getPrice: " . $e->getMessage());
            return -1;
        }
    }

    public function getDeletedComics() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE is_deleted = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Lỗi trong getDeletedComics: " . $e->getMessage());
            return false;
        }
    }

    public function restoreComic($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                error_log("ID không hợp lệ trong restoreComic: " . $id);
                return false;
            }

            $query = "UPDATE " . $this->table_name . " SET is_deleted = 0 WHERE id = :id AND is_deleted = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            error_log("Không thể khôi phục sản phẩm: " . $stmt->errorInfo()[2]);
            return false;
        } catch (PDOException $e) {
            error_log("Lỗi trong restoreComic: " . $e->getMessage());
            return false;
        }
    }

    public function searchComics($keyword) {
        $stmt = $this->conn->prepare("SELECT * FROM comics WHERE title LIKE :keyword AND is_deleted = 0");
        $stmt->execute(['keyword' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }


    public function getComicsByCategory($category_id = null, $includeDeleted = false) {
        $query = "SELECT c.* FROM comics c";
        
        $query .= " WHERE 1=1";
        
        if (!$includeDeleted) {
            $query .= " AND c.is_deleted = 0";
        }
        
        if ($category_id) {
            $query .= " AND c.category_id = :category_id";
        }
        
        $query .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getFilteredComics($filters = []) {
        try {
            $query = "SELECT p.id, p.title, p.author, p.description, p.price, p.image, p.created_at, c.name AS category_name
                      FROM " . $this->table_name . " p
                      LEFT JOIN categories c ON p.category_id = c.id
                      WHERE p.is_deleted = 0";
            
            $params = [];
            
            // Xử lý bộ lọc khoảng giá
            if (!empty($filters['price_range']) && $filters['price_range'] !== 'all') {
                switch ($filters['price_range']) {
                    case 'under_100k':
                        $query .= " AND p.price < 100000";
                        break;
                    case '100k_to_200k':
                        $query .= " AND p.price BETWEEN 100000 AND 200000";
                        break;
                    case '200k_to_300k':
                        $query .= " AND p.price BETWEEN 200000 AND 300000";
                        break;
                    case '300k_to_400k':
                        $query .= " AND p.price BETWEEN 300000 AND 400000";
                        break;
                    case '400k_to_500k':
                        $query .= " AND p.price BETWEEN 400000 AND 500000";
                        break;
                    case 'over_500k':
                        $query .= " AND p.price > 500000";
                        break;
                }
            }
            
            // Xử lý bộ lọc tác giả
            if (!empty($filters['author']) && $filters['author'] !== 'all') {
                $query .= " AND p.author = :author";
                $params[':author'] = $filters['author'];
            }
            
            // Xử lý bộ lọc thể loại
            if (!empty($filters['category_id']) && $filters['category_id'] !== 'all') {
                $query .= " AND p.category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind các tham số
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Lỗi trong getFilteredComics: " . $e->getMessage());
            return false;
        }
    }

    public function getUniqueAuthors() {
        try {
            $query = "SELECT DISTINCT author FROM " . $this->table_name . " WHERE is_deleted = 0 ORDER BY author";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (PDOException $e) {
            error_log("Lỗi trong getUniqueAuthors: " . $e->getMessage());
            return [];
        }
    }
}
?>