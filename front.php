<?php
session_start();
require_once 'classes/userClasse.php';


?>













<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnHub - Online Learning Platform</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">LearnHub</span>
                </div>

                <!-- Center Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-gray-700 hover:text-blue-600">Home</a>

                    <!-- Courses Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-1 text-gray-700 hover:text-blue-600">
                            <span>Courses</span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div class="absolute z-10 left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 invisible group-hover:visible">
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Courses</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Featured</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Popular</a>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="text-gray-700 hover:text-blue-600">Categories</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600">Instructors</a>
                </div>

                <!-- Right Side - Login/User Section -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Search Icon -->
                    <button class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Not Logged In -->
                    <?php

                    require_once 'classes/userClasse.php'; // Adjust path to where your User class is located

                    if (!isset($_SESSION['user_id'])):
                    ?>
                        <!-- Not Logged In -->
                        <div class="flex items-center space-x-3">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                <a href="aside/login.php" class="text-gray-700 hover:text-blue-600">Login</a>
                            </button>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <a href="aside/register.php" class="text-white">Sign Up</a>
                            </button>
                        </div>
                    <?php else: ?>

                        <!-- Logged In (initially hidden) -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="text-gray-600 hover:text-blue-600 relative">
                                <i class="fas fa-bell"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
                            </button>
                            <div class="relative group">
                                <div class="auth-buttons">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <span class="user-name">
                                            <?php echo htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['last_name']); ?>
                                        </span>
                                        <a href="/logout.php" class="logout-btn">Logout</a>
                                    <?php else: ?>
                                        <a href="/login.php" class="login-btn">Login</a>
                                        <a href="/signup.php" class="signup-btn">Sign Up</a>
                                    <?php endif; ?>
                                </div>
                                <
                                    <div class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 invisible group-hover:visible">
                                    <div class="py-1">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i>Profile
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-book-open mr-2"></i>My Courses
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-cog mr-2"></i>Settings
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </a>
                                    </div>
                            </div>
                        </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <div class="relative bg-blue-600 h-[600px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 text-white">
                    <h1 class="text-5xl font-bold mb-6">Transform Your Future with Online Learning</h1>
                    <p class="text-xl mb-8">Access world-class courses from expert instructors. Learn at your own pace and build the skills you need for success.</p>
                    <button class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
                        Browse Courses
                    </button>
                </div>
                <div class="md:w-1/2 mt-8 md:mt-0">
                    <img src="cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDI0LTAyL3Jhd3BpeGVsb2ZmaWNlMTVfYV9taW5pbWFsX2FuZF9sZXNzX2RldGFpbF9pbGx1c3RyYXRpb25fb2ZfYV9sb19jZDcwMDZlNi0wOWExLTQ2ZGEtOTljMi0wMmU2YTg4OTg2N2VfMS5qcGc.webp" alt="Learning" class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Course Categories</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Category 1 -->
                <div class="bg-gray-50 p-6 rounded-lg shadow hover:shadow-lg transition">
                    <div class="text-blue-600 mb-4">
                        <i class="fas fa-laptop-code text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Programming</h3>
                    <p class="text-gray-600 mb-4">Learn web development, mobile apps, and software engineering.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-700">Browse Courses →</a>
                </div>

                <!-- Category 2 -->
                <div class="bg-gray-50 p-6 rounded-lg shadow hover:shadow-lg transition">
                    <div class="text-blue-600 mb-4">
                        <i class="fas fa-chart-line text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Business</h3>
                    <p class="text-gray-600 mb-4">Master marketing, finance, and entrepreneurship skills.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-700">Browse Courses →</a>
                </div>

                <!-- Category 3 -->
                <div class="bg-gray-50 p-6 rounded-lg shadow hover:shadow-lg transition">
                    <div class="text-blue-600 mb-4">
                        <i class="fas fa-palette text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Design</h3>
                    <p class="text-gray-600 mb-4">Explore UI/UX, graphic design, and digital art.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-700">Browse Courses →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Courses -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Featured Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Course 1 -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="/api/placeholder/400/250" alt="Course" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Web Development Bootcamp</h3>
                        <p class="text-gray-600 mb-4">Learn HTML, CSS, JavaScript, and more.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 font-semibold">$99.99</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="ml-1">4.8</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 2 -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="/api/placeholder/400/250" alt="Course" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Digital Marketing Mastery</h3>
                        <p class="text-gray-600 mb-4">Master social media and SEO strategies.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 font-semibold">$89.99</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="ml-1">4.7</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 3 -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="/api/placeholder/400/250" alt="Course" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">UI/UX Design Fundamentals</h3>
                        <p class="text-gray-600 mb-4">Create beautiful and functional designs.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 font-semibold">$79.99</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="ml-1">4.9</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructors Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Meet Our Instructors</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Instructor 1 -->
                <div class="text-center">
                    <img src="/api/placeholder/200/200" alt="Instructor" class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h3 class="text-xl font-semibold mb-2">John Smith</h3>
                    <p class="text-gray-600">Web Development</p>
                    <div class="flex justify-center mt-4 space-x-3">
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

                <!-- Instructor 2 -->
                <div class="text-center">
                    <img src="/api/placeholder/200/200" alt="Instructor" class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h3 class="text-xl font-semibold mb-2">Sarah Johnson</h3>
                    <p class="text-gray-600">Digital Marketing</p>
                    <div class="flex justify-center mt-4 space-x-3">
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

                <!-- Instructor 3 -->
                <div class="text-center">
                    <img src="/api/placeholder/200/200" alt="Instructor" class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h3 class="text-xl font-semibold mb-2">Michael Chen</h3>
                    <p class="text-gray-600">UI/UX Design</p>
                    <div class="flex justify-center mt-4 space-x-3">
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

                <!-- Instructor 4 -->
                <div class="text-center">
                    <img src="/api/placeholder/200/200" alt="Instructor" class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h3 class="text-xl font-semibold mb-2">Emma Davis</h3>
                    <p class="text-gray-600">Business Strategy</p>
                    <div class="flex justify-center mt-4 space-x-3">
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-600 hover:text-blue-700"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">LearnHub</h3>
                    <p class="text-gray-400">Empowering learners worldwide with quality online education.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Courses</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Instructors</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Categories</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Programming</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Business</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Design</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Marketing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center"><i class="fas fa-envelope mr-2"></i> info@learnhub.com</li>
                        <li class="flex items-center"><i class="fas fa-phone mr-2"></i> +1 234 567 890</li>
                        <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> 123 Education St, City</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 LearnHub. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>