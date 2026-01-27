<?php
session_start();
require "includes/String.php";
require_once "functions.php";
$db = new mysql_like("db.db");
require_once "config.php";

if(isset($_COOKIE['chat_user_id'])){
    $user = getData("users", [
        'id' => $_COOKIE['chat_user_id']
    ]);
    if($user){
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        header("Location: chat/");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$config['name'];?> - Connect Seamlessly</title>
    <script src="tailwind.js"></script>
    <link href="uploads/<?=$config['favicon'];?>" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
            animation: float 20s infinite;
        }
        .shape-1 {
            width: 300px;
            height: 300px;
            background: #ff0080;
            top: -50px;
            left: -100px;
            animation-delay: 0s;
        }
        .shape-2 {
            width: 400px;
            height: 400px;
            background: #7928ca;
            bottom: -100px;
            right: -100px;
            animation-delay: -5s;
        }
        .shape-3 {
            width: 200px;
            height: 200px;
            background: #0070f3;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -50px) rotate(10deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }
        
        .hero-text-gradient {
            background: linear-gradient(to right, #fff, #b3c7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen text-white overflow-hidden relative selection:bg-indigo-500 selection:text-white">

    <!-- Decorative Background Shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <!-- Navigation -->
    <nav class="glass-nav fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-comments text-2xl text-white"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight"><?=$config['name'];?></span>
                </div>
                <!-- Desktop Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="login.php" class="text-white/80 hover:text-white font-medium transition-colors px-4 py-2">Sign In</a>
                    <a href="register.php" class="bg-white text-indigo-900 hover:bg-indigo-50 font-semibold px-6 py-2.5 rounded-full transition-all shadow-lg hover:shadow-white/25 transform hover:-translate-y-0.5">
                        Get Started
                    </a>
                </div>
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button class="text-white hover:text-gray-200">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative pt-32 pb-16 px-4 sm:px-6 lg:px-8 h-screen flex flex-col justify-center items-center">
        
        <div class="text-center max-w-4xl mx-auto z-10">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card mb-8 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                <span class="text-sm font-medium text-white/90">New Features Available</span>
            </div>

            <!-- Hero Title -->
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 leading-tight">
                Connect with anyone, <br />
                <span class="hero-text-gradient">anywhere, anytime.</span>
            </h1>

            <!-- Hero Description -->
            <p class="text-lg md:text-xl text-indigo-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                Experience seamless communication with our modern, secure, and intuitive chat platform. Join thousands of users connecting today.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
                <a href="register.php" class="w-full sm:w-auto bg-white text-indigo-900 font-bold text-lg px-8 py-4 rounded-2xl shadow-xl hover:shadow-2xl hover:bg-indigo-50 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 group">
                    Start Chatting Free
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="login.php" class="w-full sm:w-auto glass-card text-white hover:bg-white/20 font-semibold text-lg px-8 py-4 rounded-2xl transition-all border border-white/30 flex items-center justify-center">
                    Existing User?
                </a>
            </div>

            <!-- Stats/Social Proof -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white/80">
                <div>
                    <div class="text-3xl font-bold text-white"><?=number_format(count(getAll("users")));?></div>
                    <div class="text-sm">Active Users</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-white"><?=number_format(count(getAll("messages")));?></div>
                    <div class="text-sm">Messages/Day</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-white">99.9%</div>
                    <div class="text-sm">Uptime</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-white">4.9/5</div>
                    <div class="text-sm">User Rating</div>
                </div>
            </div>
        </div>

    </main>
    
    <!-- Simple Footer -->
    <footer class="absolute bottom-4 w-full text-center text-white/40 text-sm">
        &copy; <?php echo date('Y'); ?> Chat System. All rights reserved.
    </footer>

</body>
</html>
