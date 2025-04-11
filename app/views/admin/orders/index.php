<h2>Quản Lý Đơn Hàng</h2>

<?php if (!empty($orders)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách Hàng</th>
                <th>Tổng Tiền</th>
                <th>Ngày Đặt</th>
                <th>Hành Động</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order->order_id); ?></td>
                    <td><?php echo htmlspecialchars($order->name); ?> (<?php echo htmlspecialchars($order->username); ?>)</td>
                    <td><?php echo number_format($order->total, 2); ?> VNĐ</td>
                    <td><?php echo htmlspecialchars($order->order_date); ?></td>
                    <!-- Dropdown chọn trạng thái -->


                    <td>
                        <a href="index.php?controller=order&action=show&order_id=<?php echo $order->order_id; ?>"
                            class="btn btn-info btn-sm">Xem Chi Tiết</a>


                    </td>
                    <td>
    <!-- Dropdown chọn trạng thái -->
    <form action="index.php?controller=order&action=updateStatus" method="POST" style="display:inline-block;">
        <input type="hidden" name="order_id" value="<?php echo $order->order_id; ?>">
        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="Đang chờ xác nhận" <?php echo $order->status === 'Đang chờ xác nhận' ? 'selected' : ''; ?>>Đang chờ xác nhận</option>
            <option value="Đang giao" <?php echo $order->status === 'Đang giao' ? 'selected' : ''; ?>>Đang giao</option>
            <option value="Đã giao" <?php echo $order->status === 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
            <option value="Đã hủy" <?php echo $order->status === 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
        </select>
        <!-- Đã bỏ nút submit và thêm onchange để tự động submit -->
    </form>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Không có đơn hàng nào để hiển thị.</p>
<?php endif; ?>