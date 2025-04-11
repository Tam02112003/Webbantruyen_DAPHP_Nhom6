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