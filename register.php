<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Chat System</title>
    <?php
    require 'links.php';
    ?>
</head>
<body class="min-h-screen flex items-center justify-center p-4 py-8">

    <div class="glass-card w-full max-w-md rounded-2xl p-8 sm:p-10 transform transition-all duration-300 hover:scale-[1.01]">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
            <p class="text-gray-500 text-sm">Join the community and start chatting</p>
        </div>

        <form action="login.php" method="GET" class="space-y-5" id="register_form">
            
            <!-- Full Name Input -->
            <div class="input-group relative">
                <input type="text" id="fullname" name="fullname" required
                    class="block w-full px-4 py-3.5 text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-transparent"
                    placeholder="Full Name">
                <label for="fullname" 
                    class="absolute left-4 top-3.5 text-gray-400 origin-[0]">
                    Full Name
                </label>
            </div>

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

            <!-- Confirm Password Input -->
            <div class="input-group relative">
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="block w-full px-4 py-3.5 text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-transparent"
                    placeholder="Confirm Password">
                <label for="confirm_password" 
                    class="absolute left-4 top-3.5 text-gray-400 origin-[0]">
                    Confirm Password
                </label>
            </div>

            <div class="flex items-start mt-4">
                <div class="flex items-center h-5">
                    <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="text-gray-500">I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Privacy Policy</a></label>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-xl shadow-lg hover:shadow-indigo-500/30 transform transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 mt-2">
                Create Account
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Already have an account? 
            <a href="login.php" class="text-indigo-600 hover:text-indigo-700 font-semibold transition-colors">Sign in instead</a>
        </div>

    </div>

</body>
<script>
    $(document).ready(function() {
        $("#register_form").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "api/index.php",
                type: "POST",
                data: $("#register_form").serialize(),
                success: function(data) {
                    try{
                        var data = JSON.parse(data);
                        if(data.status) {
                            window.location.href = "chat/";
                        } else {
                            alert(data.message);
                        }
                    }catch(e){
                        alert(e+' '+data);
                    }
                }
            });
        });
    });
</script>
</html>
