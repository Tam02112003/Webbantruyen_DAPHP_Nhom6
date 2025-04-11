<h2>Hóa Đơn Thanh Toán</h2>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($order) && isset($orderDetails)): ?>
    <!-- Thông tin khách hàng -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Thông Tin Khách Hàng</h4>
        </div>
        <div class="card-body">
            <p><strong>Họ và Tên:</strong> <?php echo htmlspecialchars($order->name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order->email); ?></p>
            <p><strong>Số Điện Thoại:</strong> <?php echo htmlspecialchars($order->phone); ?></p>
            <p><strong>Địa Chỉ:</strong> <?php echo htmlspecialchars($order->address); ?></p>
            <p><strong>Ngày Đặt Hàng:</strong> <?php echo htmlspecialchars($order->order_date); ?></p>
            <p><strong>Trạng thái đơn hàng:</strong> <?php echo htmlspecialchars($order->status); ?></p>
        </div>
    </div>

    <!-- Chi tiết đơn hàng -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Chi Tiết Đơn Hàng</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($orderDetails)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên Comic</th>
                            <th>Số Lượng</th>
                            <th>Giá</th>
                            <th>Thành Tiền</th>

                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->title); ?></td>
                                <td><?php echo htmlspecialchars($item->quantity); ?></td>
                                <td><?php echo number_format($item->price, 2); ?> VNĐ</td>
                                <td><?php echo number_format($item->quantity * $item->price, 2); ?> VNĐ</td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Tổng Tiền:</strong></td>
                            <td><?php echo number_format($order->total, 2); ?> VNĐ</td>
                        </tr>
                    </tfoot>
                </table>
                <table class="order-details">
                    <!-- ... các dòng khác ... -->
                    <tr>
                        <td>Tạm tính:</td>
                        <td><?= number_format($order->subtotal) ?>₫</td>
                    </tr>
                    <tr>
                        <td>Phí vận chuyển:</td>
                        <td><?= number_format($order->shipping_fee) ?>₫</td>
                    </tr>
                    <tr class="total">
                        <td>Tổng cộng:</td>
                        <td><?= number_format($order->total) ?>₫</td>
                    </tr>
                </table>
            <?php else: ?>
                <p>Không có chi tiết đơn hàng để hiển thị.</p>
            <?php endif; ?>
        </div>
    </div>
    <a href="index.php?controller=order&action=history" class="btn btn-secondary">Xem lịch sử đơn hàng</a>
    <a href="index.php?controller=home&action=index" class="btn btn-primary">Tiếp tục mua sắm</a>
<?php endif; ?>