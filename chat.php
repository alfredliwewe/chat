<?php 
// chat.php 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Messaging App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
        }
        /* Custom Scrollbar */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .message-bubble {
            max-width: 75%;
        }
    </style>
</head>
<body class="h-screen flex overflow-hidden bg-gray-100">

    <!-- Sidebar -->
    <aside class="w-full md:w-80 lg:w-96 bg-white border-r border-gray-200 flex flex-col h-full z-20 absolute md:relative transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
        <!-- Sidebar Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-gray-100 bg-white">
            <div class="flex items-center space-x-3">
                <img src="https://ui-avatars.com/api/?name=Me&background=667eea&color=fff" alt="My Profile" class="w-10 h-10 rounded-full border-2 border-indigo-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">My Chat</h2>
                    <p class="text-xs text-green-500 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Online
                    </p>
                </div>
            </div>
            <a href="login.php" class="text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </a>
        </div>

        <!-- Search -->
        <div class="p-4 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div class="relative">
                <span class="absolute left-4 top-3.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" placeholder="Search chats..." class="w-full bg-gray-50 text-gray-700 rounded-xl py-3 pl-11 pr-4 border-none focus:ring-2 focus:ring-indigo-100 outline-none placeholder-gray-400 transition-all">
            </div>
        </div>

        <!-- Chat List -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            <!-- Active Chat Item -->
            <div class="flex items-center p-3 rounded-xl bg-indigo-50 cursor-pointer border border-indigo-100 transition-all">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=Alice+Smith&background=random" class="w-12 h-12 rounded-full object-cover">
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex justify-between items-baseline mb-1">
                        <h3 class="font-semibold text-gray-800">Alice Smith</h3>
                        <span class="text-xs text-indigo-600 font-medium">10:42 AM</span>
                    </div>
                    <p class="text-sm text-indigo-800 truncate">Are we still meeting later?</p>
                </div>
            </div>

            <!-- Inactive Chat Items -->
            <div class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all group">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=Bob+Johnson&background=random" class="w-12 h-12 rounded-full object-cover grayscale group-hover:grayscale-0 transition-all">
                    <time class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 border-2 border-white rounded-full"></time>
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex justify-between items-baseline mb-1">
                        <h3 class="font-semibold text-gray-700 group-hover:text-gray-900">Bob Johnson</h3>
                        <span class="text-xs text-gray-400">Yesterday</span>
                    </div>
                    <p class="text-sm text-gray-500 group-hover:text-gray-600 truncate">Thanks for the files!</p>
                </div>
            </div>

             <div class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all group">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=Charlie+Day&background=random" class="w-12 h-12 rounded-full object-cover grayscale group-hover:grayscale-0 transition-all">
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex justify-between items-baseline mb-1">
                        <h3 class="font-semibold text-gray-700 group-hover:text-gray-900">Charlie Day</h3>
                        <span class="text-xs text-gray-400">Mon</span>
                    </div>
                    <p class="text-sm text-gray-500 group-hover:text-gray-600 truncate">Can you check the email?</p>
                </div>
            </div>
             <!-- More items as fillers -->
             <div class="flex items-center p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all group">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=David+Lee&background=random" class="w-12 h-12 rounded-full object-cover grayscale group-hover:grayscale-0 transition-all">
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex justify-between items-baseline mb-1">
                        <h3 class="font-semibold text-gray-700 group-hover:text-gray-900">David Lee</h3>
                        <span class="text-xs text-gray-400">Sun</span>
                    </div>
                    <p class="text-sm text-gray-500 group-hover:text-gray-600 truncate">Have a good weekend!</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div class="fixed inset-0 bg-black/50 z-10 hidden md:hidden" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Main Chat Area -->
    <main class="flex-1 flex flex-col h-full bg-white relative w-full">
        
        <!-- Chat Header -->
        <header class="h-20 flex items-center justify-between px-6 border-b border-gray-100 bg-white/80 backdrop-blur-md sticky top-0 z-10">
            <div class="flex items-center">
                <button onclick="toggleSidebar()" class="md:hidden mr-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=Alice+Smith&background=random" class="w-10 h-10 rounded-full border border-gray-200">
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div class="ml-4">
                    <h3 class="font-bold text-gray-800 text-lg leading-tight">Alice Smith</h3>
                    <p class="text-xs text-green-500">Active now</p>
                </div>
            </div>
            <div class="flex items-center space-x-6 text-gray-400">
                <button class="hover:text-indigo-600 transition-colors"><i class="fas fa-phone-alt"></i></button>
                <button class="hover:text-indigo-600 transition-colors"><i class="fas fa-video"></i></button>
                <button class="hover:text-indigo-600 transition-colors"><i class="fas fa-info-circle"></i></button>
            </div>
        </header>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50" id="chatContainer">
            
            <div class="flex justify-center mb-4">
                <span class="text-xs text-gray-400 bg-gray-200 px-3 py-1 rounded-full">Today</span>
            </div>

            <!-- Incoming Message -->
            <div class="flex items-end">
                <img src="https://ui-avatars.com/api/?name=Alice+Smith&background=random" class="w-8 h-8 rounded-full mb-1 mr-2 invisible md:visible">
                <div class="message-bubble bg-white text-gray-700 p-4 rounded-2xl rounded-bl-sm shadow-sm border border-gray-100">
                    <p class="text-sm">Hey! How are you doing today?</p>
                    <span class="text-[10px] text-gray-400 block mt-1 text-right">10:30 AM</span>
                </div>
            </div>

            <!-- Outgoing Message -->
            <div class="flex items-end justify-end">
                <div class="message-bubble bg-indigo-600 text-white p-4 rounded-2xl rounded-br-sm shadow-md">
                    <p class="text-sm">I'm good, thanks! Just working on the new project interface. It's coming along nicely.</p>
                    <div class="flex justify-end items-center gap-1 mt-1">
                        <span class="text-[10px] text-indigo-200">10:32 AM</span>
                        <i class="fas fa-check-double text-[10px] text-indigo-200"></i>
                    </div>
                </div>
            </div>

            <!-- Incoming Message -->
            <div class="flex items-end">
                <img src="https://ui-avatars.com/api/?name=Alice+Smith&background=random" class="w-8 h-8 rounded-full mb-1 mr-2 invisible md:visible">
                <div class="message-bubble bg-white text-gray-700 p-4 rounded-2xl rounded-bl-sm shadow-sm border border-gray-100">
                    <p class="text-sm">That sounds great! Can you show me what you have so far?</p>
                    <span class="text-[10px] text-gray-400 block mt-1 text-right">10:35 AM</span>
                </div>
            </div>
            
             <!-- Outgoing Message -->
            <div class="flex items-end justify-end">
                <div class="message-bubble bg-indigo-600 text-white p-4 rounded-2xl rounded-br-sm shadow-md">
                    <p class="text-sm">Sure, let me send you a screenshot.</p>
                     <div class="flex justify-end items-center gap-1 mt-1">
                        <span class="text-[10px] text-indigo-200">10:36 AM</span>
                        <i class="fas fa-check-double text-[10px] text-indigo-200"></i>
                    </div>
                </div>
            </div>

            <!-- Incoming Message -->
            <div class="flex items-end">
                <img src="https://ui-avatars.com/api/?name=Alice+Smith&background=random" class="w-8 h-8 rounded-full mb-1 mr-2 invisible md:visible">
                <div class="message-bubble bg-white text-gray-700 p-4 rounded-2xl rounded-bl-sm shadow-sm border border-gray-100">
                     <p class="text-sm">Are we still meeting later?</p>
                    <span class="text-[10px] text-gray-400 block mt-1 text-right">10:42 AM</span>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100">
            <form action="#" onsubmit="event.preventDefault();" class="flex items-center gap-2">
                <button type="button" class="text-gray-400 hover:text-indigo-600 p-3 rounded-full hover:bg-gray-50 transition-colors">
                    <i class="fas fa-paperclip"></i>
                </button>
                <div class="flex-1 relative">
                    <input type="text" placeholder="Type a message..." 
                        class="w-full bg-gray-50 text-gray-700 rounded-full py-3.5 pl-5 pr-12 focus:ring-2 focus:ring-indigo-500 focus:bg-white border-transparent focus:border-transparent transition-all outline-none">
                    <button type="button" class="absolute right-2 top-1.5 text-gray-400 hover:text-indigo-600 p-2 rounded-full transition-colors">
                         <i class="far fa-smile"></i>
                    </button>
                </div>
                <button type="submit" class="bg-indigo-600 text-white p-3.5 rounded-full hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transform transition-transform hover:scale-105 active:scale-95">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>

    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
