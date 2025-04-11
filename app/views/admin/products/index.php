<h2>Comics</h2>

<a  href="index.php?controller=comic&action=add" class="btn btn-primary">Thêm sản phẩm</a>
<table class="table table-bordered">
    <thead>
        <tr>
            
            <th>Name</th>
            <th>Description</th>
            <th>Author</th>
            <th>Price</th>
            <th>Image</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($comics)): ?>
            <tr>
                <td colspan="7" class="text-center">Không có sản phẩm nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($comics as $comic): ?>
                <tr>
                    
                    <td><?php echo htmlspecialchars($comic->title); ?></td>
                    <td><?php echo htmlspecialchars($comic->description); ?></td>
                    <td><?php echo htmlspecialchars($comic->author);?></td>
                    <td><?php echo number_format($comic->price, 0, ',', '.') . ' VNĐ'; ?></td>
                    <td>
                    <img src="<?php echo htmlspecialchars($comic->image); ?>" style="width: 100px ; height: 100px" alt="<?php echo htmlspecialchars($comic->title); ?>">
                    </td>
                    <td><?php echo htmlspecialchars($comic->category_name); ?></td>
                    <td>
                        <a href="index.php?controller=comic&action=edit&id=<?php echo htmlspecialchars($comic->id); ?>"
                            class="btn btn-warning btn-sm">Edit</a>
                        <a href="index.php?controller=comic&action=delete&id=<?php echo htmlspecialchars($comic->id); ?>"
                            class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>