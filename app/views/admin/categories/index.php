<h2>Categories</h2>
<a href="index.php?controller=category&action=add" class="btn btn-primary mb-3">Add Category</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?php echo $category->id; ?></td>
                <td><?php echo $category->name; ?></td>
                <td>
                    <a href="index.php?controller=category&action=edit&id=<?php echo $category->id; ?>">Edit</a>
                    <a href="index.php?controller=category&action=delete&id=<?php echo $category->id; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>