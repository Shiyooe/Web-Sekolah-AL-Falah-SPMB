document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('inputPasswordConfirm');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Reset previous error messages
        removeErrorMessages();

        // Validate passwords match
        if (password.value !== confirmPassword.value) {
            showError('Passwords do not match', confirmPassword);
            return;
        }

        // Get form data
        const formData = new FormData(form);

        try {
            const response = await fetch('./adminregister.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.text();
            
            // Check if the response is JSON
            try {
                const jsonData = JSON.parse(data);
                if (jsonData.errors) {
                    jsonData.errors.forEach(error => {
                        showError(error);
                    });
                } else if (jsonData.success) {
                    alert('Registration successful! Please login.');
                    window.location.href = 'login.php';
                }
            } catch (e) {
                // If response is not JSON, check if it contains error messages
                if (data.includes('error')) {
                    showError('Registration failed. Please try again.');
                } else {
                    // Assume success if redirected
                    window.location.href = 'login.php';
                }
            }
        } catch (error) {
            showError('An error occurred. Please try again.');
            console.error('Error:', error);
        }
    });

    function showError(message, field = null) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-2';
        errorDiv.textContent = message;

        if (field) {
            field.parentNode.appendChild(errorDiv);
            field.focus();
        } else {
            form.insertBefore(errorDiv, form.firstChild);
        }
    }

    function removeErrorMessages() {
        const errors = document.querySelectorAll('.alert-danger');
        errors.forEach(error => error.remove());
    }
});
