<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Corona Dashboard</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg p-4 flex flex-col">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                    <i data-feather="user" class="text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800">Henry Klein</h2>
                    <p class="text-sm text-gray-500">Administrator</p>
                </div>
            </div>

            <nav class="space-y-2 flex-1">
                <!-- Users Section -->
                <div class="text-gray-800 font-medium px-4 py-2 mb-2">Main Menu</div>

                <a href="/users" class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-lg flex items-center space-x-3 font-medium">
                    <i data-feather="users" class="w-4 h-4"></i>
                    <span>Users</span>
                </a>

                <!-- Courses Section -->
                <a href="aside/users.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="book-open" class="w-4 h-4"></i>
                    <span>Courses</span>
                </a>

                <!-- Categories Section -->
                <a href="aside/categories.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="folder" class="w-4 h-4"></i>
                    <span>Categories</span>
                </a>

                <!-- Tags Section -->
                <a href="aside/courses.php" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="tag" class="w-4 h-4"></i>
                    <span>Tags</span>
                </a>

                <!-- Statistics Section -->
                <a href="" class="text-gray-600 px-4 py-2.5 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <i data-feather="bar-chart-2" class="w-4 h-4"></i>
                    <span>Statistics</span>
                </a>
            </nav>

            <!-- Logout Button -->
            <a href="/logout" class="mt-4 flex items-center space-x-2 text-gray-600 hover:text-red-600 px-4 py-2.5 rounded-lg transition-colors">
                <i data-feather="log-out" class="w-4 h-4"></i>
                <span>Logout</span>
            </a>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 bg-gray-50">
            <!-- Stats Grid -->
            <div class="grid grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">$12.34</h3>
                            <p class="text-green-500 flex items-center">
                                <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                                +3.5%
                            </p>
                            <p class="text-gray-500 font-medium">Growth Rate</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                            <i data-feather="dollar-sign" class="text-blue-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">$17.34</h3>
                            <p class="text-green-500 flex items-center">
                                <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                                +11%
                            </p>
                            <p class="text-gray-500 font-medium">Current Revenue</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                            <i data-feather="shopping-bag" class="text-green-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">$12.34</h3>
                            <p class="text-red-500 flex items-center">
                                <i data-feather="trending-down" class="w-4 h-4 mr-1"></i>
                                -2.4%
                            </p>
                            <p class="text-gray-500 font-medium">Daily Income</p>
                        </div>
                        <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                            <i data-feather="credit-card" class="text-red-500"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">$31.53</h3>
                            <p class="text-green-500 flex items-center">
                                <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
                                +3.5%
                            </p>
                            <p class="text-gray-500 font-medium">Total Expenses</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-50 rounded-full flex items-center justify-center">
                            <i data-feather="activity" class="text-purple-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Grid -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Transaction History -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i data-feather="refresh-cw" class="w-5 h-5 mr-2 text-blue-500"></i>
                        Recent Transactions
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i data-feather="paypal" class="w-5 h-5 text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">PayPal Transfer</p>
                                    <p class="text-sm text-gray-500">07 Jan 2019, 09:12AM</p>
                                </div>
                            </div>
                            <span class="text-gray-800 font-semibold">$236</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i data-feather="credit-card" class="w-5 h-5 text-purple-500"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Stripe Payment</p>
                                    <p class="text-sm text-gray-500">07 Jan 2019, 09:12AM</p>
                                </div>
                            </div>
                            <span class="text-gray-800 font-semibold">$593</span>
                        </div>
                    </div>
                </div>

                <!-- Open Projects -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i data-feather="folder" class="w-5 h-5 mr-2 text-blue-500"></i>
                        Active Projects
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <i data-feather="layout" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Dashboard Design</p>
                                    <p class="text-sm text-gray-500">Web Application Mockup</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Updated 15m ago</p>
                                <p class="text-sm text-blue-500 font-medium">30 tasks, 5 issues</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                    <i data-feather="code" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Website Development</p>
                                    <p class="text-sm text-gray-500">New Feature Implementation</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Updated 1h ago</p>
                                <p class="text-sm text-blue-500 font-medium">23 tasks, 5 issues</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        feather.replace();
    </script>
</body>

</html>