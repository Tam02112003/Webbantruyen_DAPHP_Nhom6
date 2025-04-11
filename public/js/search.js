document.getElementById('search-input').addEventListener('input', function() {
    var keyword = this.value.trim();
    var suggestionsBox = document.getElementById('suggestions');
    
    if (keyword.length > 1) {
        fetch('index.php?controller=home&action=autocomplete&keyword=' + encodeURIComponent(keyword))
            .then(response => response.json())
            .then(data => {
                suggestionsBox.innerHTML = '';
                
                if (data.length > 0) {
                    suggestionsBox.style.display = 'block';
                    
                    data.forEach(function(comic) {
                        var price = Math.floor(comic.price).toLocaleString('vi-VN');
                        
                        var suggestionItem = document.createElement('div');
                        suggestionItem.className = 'suggestion-item';
                        
                        suggestionItem.innerHTML = `
                            <div class="suggestion-content">
                                <div class="suggestion-image">
                                    <img src="${comic.image}" alt="${comic.title}">
                                </div>
                                <div class="suggestion-info">
                                    <div class="suggestion-title">${comic.title}</div>
                                    <div class="suggestion-meta">
                                        <span class="suggestion-price">${price} VNĐ</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        suggestionItem.addEventListener('click', function() {
                            window.location.href = `index.php?controller=home&action=detail&id=${comic.id}`;
                        });
                        
                        suggestionsBox.appendChild(suggestionItem);
                    });
                } else {
                    suggestionsBox.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                suggestionsBox.style.display = 'none';
            });
    } else {
        suggestionsBox.style.display = 'none';
    }
});