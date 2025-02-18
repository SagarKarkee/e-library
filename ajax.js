document.addEventListener('DOMContentLoaded', () => {
    // Handle all AJAX forms
    document.querySelectorAll('.ajax-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const action = form.id === 'loginForm' ? 'login' : 
                         form.id === 'registerForm' ? 'register' : 
                         'add_to_cart';

            formData.append('action', action);
            
            try {
                const response = await fetch('ajax_handler.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    showMessage(result.message, 'success');
                    if (result.redirect) {
                        setTimeout(() => window.location.href = result.redirect, 1500);
                    }
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        });
    });

    function showMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.innerHTML = `
            <span>${message}</span>
            <i class="fa-solid fa-xmark" onclick="this.parentElement.remove();"></i>
        `;
        document.body.prepend(messageDiv);
        setTimeout(() => messageDiv.remove(), 5000);
    }
});