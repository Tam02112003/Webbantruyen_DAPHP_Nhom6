<h2>Add Product</h2>
<form action="index.php?controller=comic&action=add" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Product Title:</label>
        <input type="text" id="title" name="title" class="form-control" required>
        <?php if (!empty($errors['title'])): ?>
            <div class="text-danger"><?php echo htmlspecialchars($errors['title']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Description:</label>
        <textarea id="description" name="description" class="form-control" required></textarea>
        <?php if (!empty($errors['description'])): ?>
            <div class="text-danger"><?php echo htmlspecialchars($errors['description']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <label for="price" class="form-label">Price:</label>
        <input type="number" id="price" name="price" step="0.01" class="form-control" required>
        <?php if (!empty($errors['price'])): ?>
            <div class="text-danger"><?php echo htmlspecialchars($errors['price']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <label for="author" class="form-label">Author:</label>
        <input type="text" id="author" name="author" class="form-control" required>
        <?php if (!empty($errors['author'])): ?>
            <div class="text-danger"><?php echo htmlspecialchars($errors['author']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <label for="image" class="form-label">Image:</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
        <small>Chấp nhận dạng file 'jpg', 'jpeg', 'png', 'webp'</small>
        <?php if (!empty($errors['image'])): ?>
            <div class="text-danger"><?php echo htmlspecialchars($errors['image']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Category:</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category->id; ?>"><?php echo htmlspecialchars($category->name); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['category_id'])): ?>
            <div class="text-danger"><?php echo htmlspecialchars($errors['category_id']); ?></div>
        <?php endif; ?>
    </div>
    
    <div>
        <button type="submit" class="btn btn-primary">Add Comic</button>
    </div>
</form>