<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chat System</title>
    <script src="tailwind.js"></script>
    <!--jquery-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }
        /* Floating Label Fix */
        .input-group label {
            transition: all 0.2s ease-out;
            pointer-events: none;
            z-index: 10;
        }
        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label {
            transform: translateY(-26px) scale(0.85); /* Adjusted translation */
            color: #667eea;
            background-color: white; /* Hide the border behind the label */
            padding: 0 6px;
            margin-left: -6px; /* Compensate padding to keep alignment */
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="glass-card w-full max-w-md rounded-2xl p-8 sm:p-10 transform transition-all duration-300 hover:scale-[1.01]">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h1>
            <p class="text-gray-500 text-sm">Enter your credentials to access your chats</p>
        </div>

        <form action="chat.php" method="GET" class="space-y-6" id="login_form">
            <input type="hidden" name="user_login" value="true">
            <!-- Email Input -->
            <div class="input-group relative">
                <input type="email" id="email" name="email" required
                    class="block w-full px-4 py-3.5 text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-transparent"
                    placeholder="Email Address">
                <label for="email" 
                    class="absolute left-4 top-3.5 text-gray-400 origin-[0]">
                    Email Address
                </label>
            </div>

            <!-- Password Input -->
            <div class="input-group relative">
                <input type="password" id="password" name="password" required
                    class="block w-full px-4 py-3.5 text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-transparent"
                    placeholder="Password">
                <label for="password" 
                    class="absolute left-4 top-3.5 text-gray-400 origin-[0]">
                    Password
                </label>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2 cursor-pointer group">
                    <input type="checkbox" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 cursor-pointer">
                    <span class="text-gray-500 group-hover:text-gray-700 transition-colors">Remember me</span>
                </label>
                <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium transition-colors">Forgot Password?</a>
            </div>

            <button type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-xl shadow-lg hover:shadow-indigo-500/30 transform transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0">
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Don't have an account? 
            <a href="register.php" class="text-indigo-600 hover:text-indigo-700 font-semibold transition-colors">Create account</a>
        </div>

    </div>

</body>
<script>
    $(document).ready(function() {
        $("#login_form").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "api/index.php",
                type: "POST",
                data: $("#login_form").serialize(),
                success: function(data) {
                    try{
                        var data = JSON.parse(data);
                        if(data.status) {
                            window.location.href = "chat/";
                        } else {
                            alert(data.message);
                        }
                    }catch(e){
                        alert(e);
                    }
                }
            });
        });
    });
</script>
</html>
