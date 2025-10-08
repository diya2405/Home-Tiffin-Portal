// Basic form validation and alerts
document.addEventListener('DOMContentLoaded', function() {
    // Validate quantity inputs (e.g., on order form)
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < 1) {
                this.value = 1;
                alert('Quantity must be at least 1.');
            }
        });
    });

    // Confirm logout or destructive actions (optional enhancement)
    const logoutLinks = document.querySelectorAll('a[href="logout.php"], a[href="../logout.php"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    });

    // Simple image preview for file uploads (e.g., add/edit tiffin)
    const imageInputs = document.querySelectorAll('input[type="file"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.style.maxWidth = '200px';
                    preview.style.marginTop = '10px';
                    // Insert after the input (customize selector if needed)
                    input.parentNode.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

// Existing code...