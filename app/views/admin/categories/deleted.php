<h2>Danh Mục Đã Xóa</h2>
<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $field => $message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if (!empty($categories)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Danh Mục</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category->id); ?></td>
                    <td><?php echo htmlspecialchars($category->name); ?></td>
                    <td>
                        <a href="index.php?controller=category&action=restore&id=<?php echo $category->id; ?>" class="btn btn-success btn-sm">Khôi Phục</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Không có danh mục nào đã xóa.</p>
<?php endif; ?>

<a href="index.php?controller=category&action=index" class="btn btn-secondary">Quay Lại</a>