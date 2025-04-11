<h2>Thông Tin Khách Hàng</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($cartItems)): ?>
    <div class="row">
        <div class="col-md-8">
            <h3>Giỏ Hàng Của Bạn</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sản Phẩm</th>
                        <th>Giá</th>
                        <th>Số Lượng</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->comic_name); ?></td>
                            <td><?php echo htmlspecialchars(number_format($item->price, 0, ',', '.')); ?>₫</td>
                            <td><?php echo htmlspecialchars($item->quantity); ?></td>
                            <td><?php echo htmlspecialchars(number_format($item->quantity * $item->price, 0, ',', '.')); ?>₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Tóm Tắt Đơn Hàng</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span><?php echo htmlspecialchars(number_format($subtotal, 0, ',', '.')); ?>₫</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span>
                            <?php if ($shipping_fee > 0): ?>
                                <?php echo htmlspecialchars(number_format($shipping_fee, 0, ',', '.')); ?>₫
                            <?php else: ?>
                                <span class="text-success">Miễn phí</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Tổng cộng:</span>
                        <span class="text-primary"><?php echo htmlspecialchars(number_format($total, 0, ',', '.')); ?>₫</span>
                    </div>
                    
                    <?php if ($subtotal < 100000): ?>
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-info-circle"></i> Mua thêm <?php echo htmlspecialchars(number_format(100000 - $subtotal, 0, ',', '.')); ?>₫ để được miễn phí vận chuyển
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        Giỏ hàng của bạn hiện đang trống.
    </div>
<?php endif; ?>

<form action="index.php?controller=order&action=checkout" method="post" class="mt-4">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="name" class="form-label">Họ và Tên:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="phone" class="form-label">Số Điện Thoại:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
        </div>
    </div>
    
    <div class="form-group mb-3">
        <label for="address" class="form-label">Địa Chỉ:</label>
        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
    </div>
    
    <div class="form-group mb-4">
        <label for="email" class="form-label">Email:</label>
        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly required>
    </div>
    
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="index.php?controller=cart&action=index" class="btn btn-outline-secondary me-md-2">
            <i class="bi bi-arrow-left"></i> Quay Lại Giỏ Hàng
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Xác Nhận Thanh Toán
        </button>
    </div>
</form>

<style>
    .card {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .card-header {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
    }
</style>