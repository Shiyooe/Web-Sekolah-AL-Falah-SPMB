<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Admin</title>
    <link rel="icon" type="image/x-icon" href="img/Alfalah.png" />

    <!-- Google Fonts: Inter + Poppins (lebih fem & modern) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
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
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

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
            animation: pulse-slow 8s infinite alternate;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(28, 144, 243, 0.2);
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
    </style>
</head>
<body class="font-sans">
    <div class="w-full max-w-sm mx-auto">
        <div class="glass-card p-8 text-center relative">
            <!-- Little femboy sparkle -->
            <div class="absolute top-4 right-4 w-3 h-3 rounded-full bg-femboy-accent animate-ping"></div>

            <!-- Header -->
            <h1 class="text-3xl font-bold text-gray mb-2">✨ Silahkan Login </h1>
            <p class="text-sm text-light-gray/80 mb-6">Masuk ke Admin Dashboard</p>

            <form action="adminlogin.php" method="POST">
                <div class="mb-5 text-left">
                    <label class="block text-xs font-semibold text-gray/80 mb-1.5">📧 Email</label>
                    <input
                        class="w-full p-3.5 rounded-xl bg-white/10 border border-white/20 text-white placeholder:text-white/50 transition-all duration-300 focus:border-femboy-accent"
                        type="email"
                        name="email"
                        placeholder="example@email.id"
                        required
                    />
                </div>

                <div class="mb-5 text-left">
                    <label class="block text-xs font-semibold text-gray/80 mb-1.5">🔒 Password</label>
                    <input
                        class="w-full p-3.5 rounded-xl bg-white/10 border border-white/20 text-white placeholder:text-white/50 transition-all duration-300 focus:border-femboy-accent"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    />
                </div>

                <div class="flex justify-between items-center mb-6 text-xs">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="w-3.5 h-3.5 accent-femboy-accent" />
                        <span class="text-gray/80">Ingatkan</span>
                    </label>
                    <a href="password.php" class="text-gray/80 hover:underline font-medium">Lupa? 😳</a>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-femboy-accent to-primary-blue text-gray font-semibold shadow-lg femboy-glow hover:opacity-90 transition-all duration-300 transform hover:scale-[1.02]">
                    ✨ Masuk Sekarang!
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray/15 text-center">
                <p class="text-xs text-light-gray/80">Belum ada akun?</p>
                <a href="register.php" class="text-gray/80 font-semibold hover:text-gray-300 transition">Daftar di sini</a>
            </div>
        </div>

        <footer class="mt-8 text-center text-xs text-gray/40">
            <div class="flex justify-center space-x-3 mb-1">
                <a href="#" class="hover:text-gray">Kebijakan Privasi</a> •
                <a href="#" class="hover:text-gray">Syarat & Ketentuan</a>
            </div>
            <p>© 2025 | Desain Admin by akrom</p>
        </footer>
    </div>

    <script src="js/login.js"></script>
</body>
</html>