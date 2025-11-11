<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Admin</title>
    <link rel="icon" type="image/x-icon" href="img/Alfalah.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#1c90f3',
                        'dark-accent': '#0f1a2b',
                        'light-blue': '#d9e6ff',
                        'soft-pink': '#ffccf0',
                        'femboy-accent': '#ff85ec'
                    },
                    fontFamily: {
                        sans: ['Inter', 'Poppins', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background: linear-gradient(135deg, #0f1a2b 0%, #1c3a5f 100%);
            background-image: url('img/blyat.jpg'), radial-gradient(circle at center, rgba(28, 144, 243, 0.15) 0%, transparent 70%);
            background-size: cover, 400% 400%;
            background-position: center, center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: 'Inter', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 133, 236, 0.2);
            border-radius: 24px;
            overflow: hidden;
            animation: float 6s ease-in-out infinite;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        input:focus {
            outline: none;
            border-color: #ff85ec;
            box-shadow: 0 0 0 3px rgba(255, 133, 236, 0.3);
        }

        .femboy-glow {
            box-shadow: 0 0 12px rgba(255, 133, 236, 0.6);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .error-message ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-message li {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md mx-auto">
        <div class="glass-card p-8 text-center relative">
            <!-- Sparkle femboy -->
            <div class="absolute top-4 right-4 w-3 h-3 rounded-full bg-femboy-accent animate-ping"></div>

            <h1 class="text-3xl font-bold text-gray mb-1">✨ Daftar Sebagai Admin</h1>
            <p class="text-sm text-light-gray/80 mb-6">Ayo jadi admin</p>

            <!-- Error container -->
            <div id="errorContainer"></div>

            <form id="registerForm" action="adminregister.php" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div class="text-left">
                        <label class="block text-xs font-semibold text-gray/80 mb-1.5">Nama Depan</label>
                        <input
                            class="w-full p-3.5 rounded-xl bg-white/10 border border-white/20 text-gray placeholder:text-gray/50 transition-all duration-300 focus:border-femboy-accent"
                            type="text"
                            name="first_name"
                            id="first_name"
                            placeholder="Nama_Depan"
                            required
                        />
                    </div>
                    <div class="text-left">
                        <label class="block text-xs font-semibold text-gray/80 mb-1.5">Nama Belakang</label>
                        <input
                            class="w-full p-3.5 rounded-xl bg-white/10 border border-white/20 text-gray placeholder:text-gray/50 transition-all duration-300 focus:border-femboy-accent"
                            type="text"
                            name="last_name"
                            id="last_name"
                            placeholder="Nama_Belakang"
                            required
                        />
                    </div>
                </div>

                <div class="mb-5 text-left">
                    <label class="block text-xs font-semibold text-gray/80 mb-1.5">📧 Email Kamu</label>
                    <input
                        class="w-full p-3.5 rounded-xl bg-white/10 border border-white/20 text-gray placeholder:text-gray/50 transition-all duration-300 focus:border-femboy-accent"
                        type="email"
                        name="email"
                        id="email"
                        placeholder="example@gmail.com"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="text-left">
                        <label class="block text-xs font-semibold text-gray/80 mb-1.5">🔒 Password</label>
                        <input
                            class="w-full p-3.5 rounded-xl bg-white/10 border border-white/20 text-gray placeholder:text-gray/50 transition-all duration-300 focus:border-femboy-accent"
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            required
                        />
                    </div>
                    <div class="text-left">
                        <label class="block text-xs font-semibold text-gray/80 mb-1.5">🔁 Konfirmasi Password</label>
                        <input
                            class="w-full p-3.5 rounded-xl bg-white/10 border border-white/20 text-gray placeholder:text-gray/50 transition-all duration-300 focus:border-femboy-accent"
                            type="password"
                            name="confirm_password"
                            id="confirm_password"
                            placeholder="••••••••"
                            required
                        />
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-femboy-accent to-primary-blue text-gray font-semibold shadow-lg femboy-glow hover:opacity-90 transition-all duration-300 transform hover:scale-[1.02]">
                    ✨ Daftar Sekarang!
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-white/15 text-center">
                <p class="text-xs text-light-gray/80">Sudah punya akun?</p>
                <a href="login.php" class="text-gray/80 font-semibold hover:text-gray-300 transition">Login Disini</a>
            </div>
        </div>

        <footer class="mt-4 text-center text-xs text-gray/40">
            <div class="flex justify-center space-x-3 mb-1">
                <a href="#" class="hover:text-gray">Kebijakan Privasi</a> •
                <a href="#" class="hover:text-gray">Syarat & Ketentuan</a>
            </div>
            <p>© 2025 | Desain by Akrom </p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const errorContainer = document.getElementById('errorContainer');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Reset previous error messages
                errorContainer.innerHTML = '';
                
                // Validate passwords match
                if (password.value !== confirmPassword.value) {
                    showErrors(['Password dan konfirmasi password tidak cocok']);
                    return;
                }
                
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Sedang memproses...';
                
                // Get form data
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('adminregister.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Show success message
                        errorContainer.innerHTML = `
                            <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #86efac; padding: 12px; border-radius: 12px; margin-bottom: 16px;">
                                ✅ ${data.message || 'Registrasi berhasil! Redirecting...'}
                            </div>
                        `;
                        
                        // Redirect after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    } else {
                        showErrors(data.errors || ['Terjadi kesalahan, silakan coba lagi']);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '✨ Daftar Sekarang!';
                    }
                    
                } catch (error) {
                    showErrors(['Terjadi kesalahan koneksi. Silakan coba lagi.']);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '✨ Daftar Sekarang!';
                    console.error('Error:', error);
                }
            });

            function showErrors(errors) {
                const errorHtml = `
                    <div class="error-message">
                        <ul>
                            ${errors.map(error => `<li>${error}</li>`).join('')}
                        </ul>
                    </div>
                `;
                errorContainer.innerHTML = errorHtml;
            }
        });
    </script>
</body>
</html>