<h2>Comic Đã Xóa</h2>

<?php if (!empty($comics)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu Đề</th>
                <th>Tác Giả</th>
                <th>Giá</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comics as $comic): ?>
                <tr>
                    <td><?php echo htmlspecialchars($comic->id); ?></td>
                    <td><?php echo htmlspecialchars($comic->title); ?></td>
                    <td><?php echo htmlspecialchars($comic->author); ?></td>
                    <td><?php echo number_format($comic->price, 2); ?> VNĐ</td>
                    <td>
                        <a href="index.php?controller=comic&action=restore&id=<?php echo $comic->id; ?>" class="btn btn-success btn-sm">Khôi Phục</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Không có comic nào đã xóa.</p>
<?php endif; ?>

<a href="index.php?controller=comic&action=index" class="btn btn-secondary">Quay Lại</a>