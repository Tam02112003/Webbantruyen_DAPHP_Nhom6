<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/search.css">
</head>
<body>
<div class="search-results-container">
    <div class="search-header">
        <h2 class="search-title">
            <i class="fas fa-search"></i> Kết quả tìm kiếm cho: 
            <span class="search-keyword"><?php echo htmlspecialchars($keyword); ?></span>
        </h2>
        <div class="result-count">
            <?php echo count($comics); ?> kết quả
        </div>
    </div>

    <?php if (!empty($comics)): ?>
        <div class="comic-results-grid">
            <?php foreach ($comics as $comic): ?>
                <div class="comic-card">
                    <div class="comic-image">
                        <img src="<?php echo htmlspecialchars($comic['image']); ?>" alt="<?php echo htmlspecialchars($comic['title']); ?>">
                    </div>
                    <div class="comic-info">
                        <h3 class="comic-title">
                            <a href="index.php?controller=home&action=detail&id=<?php echo $comic['id']; ?>">
                                <?php echo htmlspecialchars($comic['title']); ?>
                            </a>
                        </h3>
                        <div class="comic-author">
                            <i class="fas fa-user-pen"></i> <?php echo htmlspecialchars($comic['author'] ?? 'Đang cập nhật'); ?>
                        </div>
                        <div class="comic-price">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars(number_format($comic['price'])); ?> VNĐ
                        </div>
                        <a href="index.php?controller=home&action=detail&id=<?php echo $comic['id']; ?>" class="view-detail-btn">
                            Xem chi tiết <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <img src="public/images/no-results.png" alt="Không tìm thấy kết quả">
            <p>Không tìm thấy truyện nào phù hợp với từ khóa "<strong><?php echo htmlspecialchars($keyword); ?></strong>"</p>
            <a href="index.php" class="back-to-home">Quay lại trang chủ</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>


