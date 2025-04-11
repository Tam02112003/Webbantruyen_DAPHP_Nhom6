<h2>Lịch Sử Đơn Hàng</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($orders)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mã Đơn Hàng</th>
                        <th>Ngày Đặt Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Hành Động</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order->order_id); ?></td>
                            <td><?php echo htmlspecialchars($order->order_date); ?></td>
                            <td><?php echo number_format($order->total, 2); ?> VNĐ</td>
                            <td>
                                <a href="index.php?controller=order&action=orderDetail&order_id=<?php echo $order->order_id; ?>" class="btn btn-info btn-sm">Xem Chi Tiết</a>
                            </td>
                            <td><?php echo htmlspecialchars($order->status); ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Bạn chưa có đơn hàng nào.</p>
        <?php endif; ?>

        <a href="index.php?controller=home&action=index" class="btn btn-primary">Tiếp tục mua sắm</a>