<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/home.css">
    <title>Comic Store</title>
</head>
<body>
    <div class="container mt-4">
        <div class="search-container">
            <a href="index.php?controller=cart&action=index">
                <i class="fas fa-shopping-cart" style="cursor: pointer;color:black; margin-right: 30px;"></i>
            </a>
            <i class="fas fa-search" id="search-icon" style="cursor: pointer;"></i>
            <input type="text" id="search-input" name="keyword" placeholder="Nhập tên truyện để tìm kiếm" required
                class="form-control" style="display: none;">
            <button id="search-button" class="btn btn-outline-success" style="display: none;">Tìm kiếm</button>
            <div id="suggestions" class="suggestions-box"></div>
        </div>
        <form action="index.php" method="GET" class="search-form" id="search-form" style="display: none;">
            <input type="hidden" name="controller" value="home">
            <input type="hidden" name="action" value="search">
        </form>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success text-center">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?> -->
            
        <!-- Slideshow -->
        <div id="comicCarousel" class="carousel slide mb-4" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="public/slider/hinh-anh-one-piece-dep-chat-nhat.jpeg" class="d-block w-100"
                        alt="Comic Slide 1">
                </div>
                <div class="carousel-item">
                    <img src="public/slider/slide-doraemon01.jpg" class="d-block w-100" alt="Comic Slide 2">
                </div>
                <div class="carousel-item">
                    <img src="public/slider/slide-onepiece01.jpg" class="d-block w-100" alt="Comic Slide 3">
                </div>
                <div class="carousel-item">
                    <img src="public/slider/slide-tsubasa01.webp" class="d-block w-100" alt="Comic Slide 4">
                </div>
            </div>
            <a class="carousel-control-prev" href="#comicCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#comicCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    
                    <div class="stat-title">Những Bản Manga mới nhất</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                   
                    <div class="stat-title">Khách hàng thân thiết</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                   
                    <div class="stat-title">Thế Giới của Light Novel</div>
                </div>
            </div>
        </div>
    </div>
</section>

    </div>
    <div class="row">
        <!-- Cột bộ lọc -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">BỘ LỌC</h5>
                </div>
                <div class="card-body">
                    
                    <!-- Bộ lọc khoảng giá -->
                    <div class="filter-section mb-4">
                        <h6 class="font-weight-bold">KHOẢNG GIÁ</h6>
                        <div class="list-group">
                        <a href="?" class="list-group-item list-group-item-action <?= empty($_GET['price_range']) ? 'active' : '' ?>">Tất cả</a>
                            <a href="?price_range=under_100k<?= !empty($_GET['author']) ? '&author=' . $_GET['author'] : '' ?><?= !empty($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : '' ?>"
                                class="list-group-item list-group-item-action <?= ($_GET['price_range'] ?? '') === 'under_100k' ? 'active' : '' ?>">
                                Nhỏ hơn 100,000đ
                            </a>
                            <a href="?price_range=100k_to_200k<?= !empty($_GET['author']) ? '&author=' . $_GET['author'] : '' ?><?= !empty($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : '' ?>"
                                class="list-group-item list-group-item-action <?= ($_GET['price_range'] ?? '') === '100k_to_200k' ? 'active' : '' ?>">
                                Từ 100,000đ - 200,000đ
                            </a>
                           
                        </div>
                    </div>

                    <!-- Bộ lọc tác giả -->
                    <div class="filter-section mb-4">
                        <h6 class="font-weight-bold">TÁC GIẢ</h6>
                        <div class="list-group">
                        <a href="?" class="list-group-item list-group-item-action <?= empty($_GET['author']) ? 'active' : '' ?>">Tất cả</a>
                            <?php foreach ($authors as $author): ?>
                                <a href="?author=<?= urlencode($author) ?><?= !empty($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : '' ?><?= !empty($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : '' ?>"
                                    class="list-group-item list-group-item-action <?= ($_GET['author'] ?? '') === $author ? 'active' : '' ?>">
                                    <?= htmlspecialchars($author) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Bộ lọc thể loại -->
                    <div class="filter-section">
                        <h6 class="font-weight-bold">THỂ LOẠI</h6>
                        <div class="list-group">
                        <a href="?" class="list-group-item list-group-item-action <?= empty($_GET['category_id']) ? 'active' : '' ?>">Tất cả</a>
                            <?php foreach ($categories as $category): ?>
                                <a href="?category_id=<?= $category->id ?><?= !empty($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : '' ?><?= !empty($_GET['author']) ? '&author=' . $_GET['author'] : '' ?>"
                                    class="list-group-item list-group-item-action <?= ($_GET['category_id'] ?? '') == $category->id ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category->name) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cột hiển thị truyện -->
                <div class="col-md-9">
                    <!-- Phần hiển thị truyện -->
                    <div class="row">
                        <?php if (!empty($comics)): ?>
                            <?php foreach ($comics as $comic): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100 comic-card">
                                        <img src="<?php echo htmlspecialchars($comic->image); ?>" class="card-img-top"
                                            alt="<?php echo htmlspecialchars($comic->title); ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($comic->title); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($comic->description); ?></p>
                                            <p class="card-text"><strong>Giá:
                                                    <?php echo htmlspecialchars(number_format($comic->price)); ?>
                                                    VNĐ</strong></p>
                                            <form action="index.php?controller=cart&action=add" method="post"
                                                class="d-flex align-items-center">
                                                <input type="hidden" name="comic_id" value="<?php echo $comic->id; ?>">
                                                <input type="number" name="quantity" value="1" min="1" class="form-control"
                                                    style="width: 75px; margin-right: 10px;">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-shopping-cart"></i> <!-- Font Awesome Icon -->
                                                </button>
                                            </form>
                                            <a href="index.php?controller=home&action=detail&id=<?php echo htmlspecialchars($comic->id); ?>"
                                                class="btn btn-secondary mt-2">
                                                <i class="fas fa-info-circle"></i> <!-- Font Awesome Icon -->
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">Không có truyện nào để hiển thị.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>

            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="public/js/home.js"></script>
            <script src="public/js/search.js"></script>
               
            
</body>

</html>