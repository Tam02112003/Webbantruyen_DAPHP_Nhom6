<h2 class="text-center">Giỏ Hàng</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger text-center">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success text-center">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="container mt-4">
    <?php if (empty($cartItems)): ?>
        <div class="text-center">
            <p>Giỏ hàng của bạn hiện đang trống.</p>
            <a href="index.php?controller=home&action=index" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <td>Hình ảnh</td>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="cartItems">
                <?php foreach ($cartItems as $item): ?>
                    <tr data-id="<?php echo $item->comic_id; ?>">
                        <td>
                            <img src="<?php echo htmlspecialchars($item->image); ?>" alt="<?php echo htmlspecialchars($item->title); ?>" class="img-fluid" style="max-height: 100px; object-fit: cover;">
                        </td>
                        <td>
                            <a href="index.php?controller=home&action=detail&id=<?php echo $item->comic_id; ?>">
                                <?php echo htmlspecialchars($item->title); ?>
                            </a>
                        </td>
                        <td class="price"><?php echo htmlspecialchars(number_format($item->price, 2)); ?> VNĐ</td>
                        <td>
                            <div class="quantity-control">
                                <button class="btn btn-sm btn-secondary decrease">-</button>
                                <input type="number" class="quantity form-control d-inline" value="<?php echo $item->quantity; ?>" min="1" style="width: 60px; display: inline-block;" readonly>
                                <button class="btn btn-sm btn-secondary increase">+</button>
                            </div>
                            <input type="hidden" class="item-price" value="<?php echo $item->price; ?>">
                        </td>
                        <td class="subtotal"><?php echo htmlspecialchars(number_format($item->quantity * $item->price, 2)); ?> VNĐ</td>
                        <td>
                            <a href="index.php?controller=cart&action=remove&comic_id=<?php echo $item->comic_id; ?>" class="btn btn-sm btn-danger">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="text-right">Tổng cộng: <strong id="total"><?php echo htmlspecialchars(number_format($total, 2)); ?> VNĐ</strong></h3>
        <div class="text-right mt-3">
            <a href="index.php?controller=cart&action=clear" class="btn btn-danger">Xóa Giỏ Hàng</a>
            <a href="index.php?controller=order&action=customerInfo" class="btn btn-success">Thanh Toán</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.increase').click(function() {
        var row = $(this).closest('tr');
        var quantityInput = row.find('.quantity');
        var currentQuantity = parseInt(quantityInput.val());
        quantityInput.val(currentQuantity + 1);
        updateCart(row);
    });

    $('.decrease').click(function() {
        var row = $(this).closest('tr');
        var quantityInput = row.find('.quantity');
        var currentQuantity = parseInt(quantityInput.val());
        if (currentQuantity > 1) {
            quantityInput.val(currentQuantity - 1);
            updateCart(row);
        }
    });

    function updateCart(row) {
        var comicId = row.data('id');
        var quantity = row.find('.quantity').val();
        var price = parseFloat(row.find('.item-price').val().replace(/,/g, ''));

        // Cập nhật subtotal cho sản phẩm
        var subtotal = quantity * price;
        row.find('.subtotal').text(numberFormat(subtotal) + ' VNĐ');

        // Cập nhật tổng cộng
        updateTotal();

        // Gửi AJAX để cập nhật giỏ hàng trên server
        $.ajax({
            url: 'index.php?controller=cart&action=update',
            method: 'POST',
            data: {
                comic_id: comicId,
                quantity: quantity
            },
            success: function(response) {
                console.log('Cập nhật thành công');
            },
            error: function(xhr, status, error) {
                console.error('Lỗi khi cập nhật:', error);
            }
        });
    }

    function updateTotal() {
        var total = 0;
        $('#cartItems tr').each(function() {
            var price = parseFloat($(this).find('.item-price').val().replace(/,/g, ''));
            var quantity = parseInt($(this).find('.quantity').val());
            total += price * quantity;
        });
        $('#total').text(numberFormat(total) + ' VNĐ');
    }

    function numberFormat(number) {
        return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
});
</script>