document.getElementById('search-icon').addEventListener('click', function () {
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');
    const suggestions = document.getElementById('suggestions');

    if (searchInput.style.display === 'none' || searchInput.style.display === '') {
        searchInput.style.display = 'block'; // Hiện thanh tìm kiếm
        searchButton.style.display = 'inline-block'; // Hiện nút tìm kiếm
        suggestions.style.display = 'none'; // Ẩn danh sách đề xuất
        searchInput.focus(); // Đặt con trỏ vào thanh tìm kiếm
    } else {
        searchInput.style.display = 'none'; // Ẩn thanh tìm kiếm
        searchButton.style.display = 'none'; // Ẩn nút tìm kiếm
        suggestions.style.display = 'none'; // Ẩn danh sách đề xuất
    }
});

// Khi nhấn nút tìm kiếm, gửi form
document.getElementById('search-button').addEventListener('click', function () {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    if (searchInput.value) {
        searchForm.appendChild(searchInput);
        searchForm.submit(); // Gửi form
    }
});

// Xử lý khi chọn thể loại
document.querySelector('select[name="category_id"]').addEventListener('change', function () {
    if (this.value === '') {
        window.location.href = '?controller=home&action=index';
    } else {
        document.querySelector('form[method="GET"]').submit();
    }
});