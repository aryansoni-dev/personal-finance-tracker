<nav class="gradient-bg text-white p-4 relative custom-border-bottom">
    <div class="container mx-auto flex justify-between items-center">
        <div class="flex items-center justify-between gap-1">
            <!-- <img src="/financeTracker/imgs/dollar-wallet-money-svgrepo-com.svg" alt="logo" class="logo"> -->
            <a href="#" class="text-2xl font-bold">FineTrack</a>
        </div>
        <div class="flex items-center space-x-4">
            <!-- <a href="#" class="hover:text-gray-300">
                <span class="material-icons">notifications</span>
            </a> -->
            <a href="#" class="hover:text-gray-300">
                <span class="material-icons">account_circle</span>
            </a>
            <button id="mobile-menu-button" class="md:hidden hover:text-gray-300 focus:outline-none">
                <span class="material-icons">menu</span>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu">
        <div class="p-4">
            <ul class="space-y-4">
                <li>
                    <a href="./dashboard.php" id="nav-dashboard-a" class="flex items-center space-x-2 text-white/80 hover:text-gray-800 p-2 rounded focus:text-gray-800">
                        <span class="material-icons">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./profile.php" id="nav-profile-a" class="flex items-center space-x-2 text-white/80 hover:text-gray-800 p-2 rounded focus:text-gray-800">
                        <span class="material-icons">person</span>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="./analytics.php" id="nav-analytics-a" class="flex items-center space-x-2 text-white/80 hover:text-gray-800 p-2 rounded focus:text-gray-800">
                        <span class="material-icons">bar_chart</span>
                        <span>Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="./transactions.php" id="nav-transactions-a" class="flex items-center space-x-2 text-white/80 hover:text-gray-800 p-2 rounded focus:text-gray-800">
                        <span class="material-icons">bar_chart</span>
                        <span>Transaction History</span>
                    </a>
                </li>
                <li>
                    <a href="./manageBudgets.php" id="nav-budget-a" class="flex items-center space-x-2 text-white/80 hover:text-gray-800 p-2 rounded focus:text-gray-800">
                        <span class="material-icons">account_balance_wallet</span>
                        <span>Manage Budget</span>
                    </a>
                </li>
                <!-- Logout link -->
                <li>
                    <a href="/financeTracker/views/auth/logout.php"
                        class="logout flex text-red-500 hover:text-red-700 transition-all transform items-center space-x-2 focus:outline-none focus:text-red-500 hover:translate-x-2 p-2 rounded">
                        <span>Logout</span>
                        <span class="material-icons">logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>