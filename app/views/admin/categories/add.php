<h2>Thêm Danh Mục</h2>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $field => $message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="index.php?controller=category&action=add" method="post">
    <div class="mb-3">
        <label for="name" class="label-control">Category Name:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">Add Category</button>
    </div>
</form>