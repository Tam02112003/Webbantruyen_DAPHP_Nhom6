<!-- app/views/customer/comics/detail.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/detail.css">
</head>
<body>
    

<div class="comic-detail-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($comic): ?>
        <div class="comic-detail-card">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="comic-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?controller=home&action=index"><i class="fas fa-home"></i> Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="index.php?controller=home&action=index">Danh sách truyện</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($comic->title); ?></li>
                </ol>
            </nav>

            <div class="comic-detail-content">
                <!-- Hình ảnh sản phẩm -->
                <div class="comic-image-container">
                    <div class="comic-main-image">
                        <img src="<?php echo htmlspecialchars($comic->image); ?>" alt="<?php echo htmlspecialchars($comic->title); ?>" class="img-fluid">
                    </div>
                    <div class="comic-badge">
                        <span class="badge bg-danger">Mới</span>
                    </div>
                </div>

                <!-- Thông tin sản phẩm -->
                <div class="comic-info">
                    <h1 class="comic-title"><?php echo htmlspecialchars($comic->title); ?></h1>
                    
                    <div class="comic-meta">
                        <div class="meta-item">
                            <i class="fas fa-user-pen"></i>
                            <span>Tác giả: <?php echo htmlspecialchars($comic->author); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-tags"></i>
                            <span>Thể loại: <?php echo htmlspecialchars($comic->category_name); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Ngày phát hành: <?php echo date('d/m/Y', strtotime($comic->created_at)); ?></span>
                        </div>
                    </div>

                    <div class="comic-price">
                        <span class="price"><?php echo number_format($comic->price, 0, ',', '.'); ?> VNĐ</span>
                        <?php if ($comic->price > 100000): ?>
                            <span class="discount-badge">-10%</span>
                        <?php endif; ?>
                    </div>

                    <div class="comic-description">
                        <h3><i class="fas fa-info-circle"></i> Mô tả</h3>
                        <p><?php echo nl2br(htmlspecialchars($comic->description)); ?></p>
                    </div>

                    <!-- Form thêm vào giỏ hàng -->
                    <form action="index.php?controller=cart&action=add" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="comic_id" value="<?php echo htmlspecialchars($comic->id); ?>">
                        
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn minus"><i class="fas fa-minus"></i></button>
                            <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                            <button type="button" class="quantity-btn plus"><i class="fas fa-plus"></i></button>
                        </div>
                        
                        <button type="submit" class="add-to-cart-btn">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                    </form>

                    <div class="comic-actions">
                        <a href="index.php?controller=home&action=index" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                        <a href="index.php?controller=cart&action=index" class="view-cart-btn">
                            <i class="fas fa-shopping-cart"></i> Xem giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="comic-not-found">
            <div class="not-found-content">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Không tìm thấy sản phẩm</h2>
                <p>Xin lỗi, chúng tôi không thể tìm thấy truyện bạn yêu cầu.</p>
                <a href="index.php?controller=home&action=index" class="btn btn-primary">
                    <i class="fas fa-home"></i> Quay lại trang chủ
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>


<script src="public/js/detail.js"></script>


</body>
</html>