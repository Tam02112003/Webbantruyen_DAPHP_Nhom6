<h2>Chỉnh Sửa Danh Mục</h2>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $field => $message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<form action="index.php?controller=category&action=edit&id=<?php echo $category->id; ?>" method="post">
    <div class="mb-3">
        <label class="lable-control" for="name">Category Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $category->name; ?>" required>
    </div class="mb-3">
    <div>
        <button type="submit" class="btn btn-primary">Update Category</button>
    </div>
</form>