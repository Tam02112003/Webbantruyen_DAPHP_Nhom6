<h2>Chỉnh sửa comic</h2>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $field => $message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="index.php?controller=comic&action=edit&id=<?php echo $comic->id; ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Product Name:</label>
        <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($comic->title); ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description:</label>
        <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($comic->description); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="author" class="form-label">Author:</label>
        <textarea id="author" name="author" class="form-control" required><?php echo htmlspecialchars($comic->author); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price:</label>
        <input type="number" id="price" name="price" step="1" class="form-control" value="<?php echo htmlspecialchars($comic->price); ?>" required>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image:</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
        <small>Chấp nhận dạng file 'jpg', 'jpeg', 'png', 'webp'</small>
        <?php if (!empty($comic->image)): ?>
            <div class="mt-2">
                <p>Current Image:</p>
                <img src="<?php echo htmlspecialchars($comic->image); ?>" class="img-fluid" style="max-width: 200px;" alt="<?php echo htmlspecialchars($comic->title); ?>">
            </div>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label for="category_id" class="form-label">Category:</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category->id; ?>" <?php if ($category->id == $comic->category_id) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="index.php?controller=comic&action=index" class="btn btn-secondary">Cancel</a>
    </div>
</form>