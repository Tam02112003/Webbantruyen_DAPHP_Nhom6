<h2>Chi Tiết Đơn Hàng #<?php echo htmlspecialchars($order->order_id); ?></h2>

<div class="card mb-3">
    <div class="card-header">
        <h5>Thông Tin Đơn Hàng</h5>
    </div>
    <div class="card-body">
        <p><strong>ID Đơn Hàng:</strong> <?php echo htmlspecialchars($order->order_id); ?></p>
        <p><strong>Khách Hàng:</strong> <?php echo htmlspecialchars($order->name); ?> (<?php echo htmlspecialchars($order->username); ?>)</p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order->email); ?></p>
        <p><strong>Địa Chỉ:</strong> <?php echo htmlspecialchars($order->address); ?></p>
        <p><strong>Số Điện Thoại:</strong> <?php echo htmlspecialchars($order->phone); ?></p>
        <p><strong>Tổng Tiền:</strong> <?php echo number_format($order->total, 2); ?> VNĐ</p>
        <p><strong>Ngày Đặt:</strong> <?php echo htmlspecialchars($order->order_date); ?></p>
        <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($order->status); ?></p>
    </div>
</div>

<h3>Các Sản Phẩm Trong Đơn Hàng</h3>
<?php if (!empty($order_details)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Giá</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_details as $detail): ?>
                <tr>
                    <td><?php echo htmlspecialchars($detail->title); ?></td>
                    <td><?php echo htmlspecialchars($detail->quantity); ?></td>
                    <td><?php echo number_format($detail->price, 2); ?> VNĐ</td>
                    <td><?php echo number_format($detail->price * $detail->quantity, 2); ?> VNĐ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Không có sản phẩm nào trong đơn hàng này.</p>
<?php endif; ?>

<a href="index.php?controller=order&action=index" class="btn btn-secondary">Quay Lại</a>