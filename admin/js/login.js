document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Reset previous error messages
        removeErrorMessages();

        // Disable submit button and show loading state
        submitButton.disabled = true;
        const originalButtonText = submitButton.textContent;
        submitButton.textContent = 'Logging in...';

        // Get form data
        const formData = new FormData(form);

        try {
            const response = await fetch('adminlogin.php', {
                method: 'POST',
                body: formData
            });

            // Get the raw text first
            const responseText = await response.text();
            console.log('Raw response:', responseText);

            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('JSON parse error:', e);
                throw new Error('Invalid server response');
            }
            
            if (data.success) {
                showSuccess('Login successful! Redirecting...');
                setTimeout(() => {
                    // Pastikan redirect ke path yang benar
                    const redirectPath = data.redirect.startsWith('./') ? 
                        data.redirect.substring(2) : data.redirect;
                    window.location.href = redirectPath;
                }, 1000);
            } else {
                showError(data.message || 'Login failed. Please check your credentials.');
            }
        } catch (error) {
            console.error('Error details:', error);
            showError('An error occurred. Please try again.');
        } finally {
            // Re-enable submit button and restore text
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    });

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-2';
        errorDiv.textContent = message;
        form.insertBefore(errorDiv, form.firstChild);
    }

    function showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success mt-2';
        successDiv.textContent = message;
        form.insertBefore(successDiv, form.firstChild);
    }

    function removeErrorMessages() {
        const messages = document.querySelectorAll('.alert');
        messages.forEach(msg => msg.remove());
    }
});