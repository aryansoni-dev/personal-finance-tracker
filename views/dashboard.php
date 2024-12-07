<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../classes/Total.php";

$db = getDBConnection();
if (!$db) {
    throw new Exception("Database connection failed");
}

$total = new Total($db);
$balance = $total->getWalletBalance($_SESSION['userID']);
$totalIncome = $total->getTransactionTotalByType($_SESSION['userID'], "income");
$totalExpenses = $total->getTransactionTotalByType($_SESSION['userID'], "expense");
$savings = $totalIncome - $totalExpenses;
// var_dump($totalIncome);
// var_dump($totalExpenses);
// echo "Formatted Total Income: " . number_format($totalIncome, 2) . "\n";
// echo "Formatted Total Expenses: " . number_format($totalExpenses, 2) . "\n";
?>

<!DOCTYPE html>
<html lang="en">
<!-- Previous head content remains the same until style tag -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>FineTrack - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link rel="icon" href="/financeTracker/favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php require './components/_styles.php' ?>
</head>

<body class="bg-gray-100">
    <?php
    if (!isset($_SESSION['isAuthenticated']) || $_SESSION['isAuthenticated'] == false) {
    ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Imposter!',
                text: 'You need to login first!',
            }).then(() => {
                window.location.href = "/financeTracker/views/auth/login.php";
            });
        </script>
        <?php
    } else {
        if (isset($_SESSION["count"]) && $_SESSION["count"] == 1) {
        ?>
            <script>
                Swal.fire({
                    position: 'top-end',
                    // icon: 'error',
                    title: 'Welcome Back!',
                    text: 'Glad to have you back...',
                    timer: '3000',
                    showConfirmButton: false,
                });
            </script>

        <?php
        }
        ?>
        <?php require './components/_nav.php' ?>

        <div class="flex">
            <?php require './components/_aside.php' ?>

            <main class="main-content flex-1 p-6">
                <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
                <!-- Totals Cards Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md hover-scale" data-aos="fade-up">
                        <h2 class="text-xl font-semibold mb-2">Total Balance</h2>
                        <p class="text-3xl font-bold text-blue-600">₹ <?php echo number_format($balance, 2); ?> </p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md hover-scale" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="text-xl font-semibold mb-2">Income</h2>
                        <p class="text-3xl font-bold text-green-600">₹ <?php echo number_format($totalIncome, 2); ?></p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md hover-scale" data-aos="fade-up" data-aos-delay="200">
                        <h2 class="text-xl font-semibold mb-2">Expenses</h2>
                        <p class="text-3xl font-bold text-red-600">₹ <?php echo number_format($totalExpenses, 2); ?></p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md hover-scale" data-aos="fade-up" data-aos-delay="300">
                        <h2 class="text-xl font-semibold mb-2">Savings</h2>
                        <p class="text-3xl font-bold text-purple-600">₹ <?php echo number_format($savings, 2); ?></p>
                    </div>
                </div>
                <!-- Recent Transactions Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md" data-aos="fade-right">
                        <h2 class="text-xl font-semibold mb-4">Recent Transactions</h2>
                        <ul class="space-y-4" id="recent_transactions">
                            <!-- Will be inserted dynamically -->
                        </ul>
                    </div>
                    <!-- Budget Section -->
                    <div class="bg-white p-6 rounded-lg shadow-md" data-aos="fade-left">
                        <h2 class="text-xl font-semibold mb-4">Budget Overview</h2>
                        <div class="space-y-4" id="budget_overview">
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span>Food & Dining</span>
                                    <span>70%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 70%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span>Transportation</span>
                                    <span>45%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span>Entertainment</span>
                                    <span>90%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-red-600 h-2.5 rounded-full" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script>
            const aside_dashboard = document.getElementById('dashboard-a');
            const nav_dashboard = document.getElementById('nav-dashboard-a');
            document.addEventListener('DOMContentLoaded', () => {
                aside_dashboard.classList.add('aside-a-bg', 'text-blue-500', 'translate-x-2');
                nav_dashboard.classList.add('nav-a-bg', 'text-gray-800', 'translate-x-2');

                fetchRecentTransactions();
                fetchBudgetOverview();
                // createBudgetDiv("Food", 50);

                function fetchRecentTransactions() {
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', './transaction/fetchRecentTransactions.php', true);

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                try {
                                    // Parse the server response
                                    const response = JSON.parse(xhr.responseText);
                                    // Check if the response has the expected structure
                                    if (response.status === 'success' && Array.isArray(response.data)) {
                                        recentTransactions = response.data;
                                        // console.log(recentTransactions);
                                        // Sort transactions by ID (or any other field you prefer)
                                        recentTransactions.sort((a, b) => b.id - a.id);
                                        // Update the table with the new transactions
                                        updateRecentTransactions(recentTransactions);
                                    } else {
                                        updateRecentTransactions(response.data);
                                    }
                                } catch (error) {
                                    // Swal.fire({
                                    //     icon: 'error',
                                    //     title: 'Error',
                                    //     text: 'Invalid response from the server. Please check the server logs.',
                                    // });
                                    console.error('Error parsing server response:', error);
                                    updateRecentTransactions([]);
                                }
                            } else {
                                // Handle non-200 HTTP statuses
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: `Server Error: ${xhr.status} - ${xhr.statusText}`,
                                });
                            }
                        }
                    };

                    xhr.send();
                }

                function updateRecentTransactions(RecentTransactions) {
                    // console.log(RecentTransactions);
                    const recent_transactions = document.querySelector('#recent_transactions');

                    if (RecentTransactions.length < 1 || !RecentTransactions) {
                        const li = document.createElement('li');
                        li.className = 'flex justify-start items-center text-gray-500';
                        li.innerHTML = `
                            <span class="material-icons text-gray-500 mr-2">info</span>
                            No transactions have been made yet.
                        `;
                        recent_transactions.appendChild(li);
                        return;
                    }

                    RecentTransactions.forEach(transaction => {
                        let icon, iconColor, amountColor;
                        if (transaction.category_type === "income") {
                            icon = "attach_money";
                            iconColor = "green-600";
                            amountColor = "green-600";
                        } else if (transaction.category_type === "expense" && transaction.category_name === "Shopping" || transaction.category_name === "Groceries") {
                            icon = "shopping_cart";
                            iconColor = "blue-600";
                            amountColor = "red-600";
                        } else if (transaction.category_type === "expense" && transaction.category_name === "Food") {
                            icon = "restaurant";
                            iconColor = "purple-600";
                            amountColor = "red-600";
                        } else if (transaction.category_type === "expense" && transaction.category_name === "Bills") {
                            icon = "receipt_long";
                            iconColor = "pink-600";
                            amountColor = "red-600";
                        } else if (transaction.category_type === "expense" && transaction.category_name === "Entertainment") {
                            icon = "mood";
                            iconColor = "yellow-600";
                            amountColor = "red-600";
                        }

                        // Create the list item
                        const li = document.createElement("li");
                        li.className = "flex justify-between items-center";
                        // Create the div for the icon and label
                        const div = document.createElement("div");
                        div.className = "flex items-center space-x-2";
                        // Create the icon
                        const iconElement = document.createElement("span");
                        iconElement.className = `material-icons text-${iconColor}`;
                        iconElement.textContent = icon;
                        // Create the label
                        const label = document.createElement("span");
                        label.textContent = transaction.description;
                        // Append icon and label to the div
                        div.appendChild(iconElement);
                        div.appendChild(label);
                        // Create the amount span
                        const amount = document.createElement("span");
                        amount.className = `text-${amountColor}`;
                        amount.textContent = (transaction.category_type === "income" ? "+" : "-") + `₹ ${parseFloat(transaction.amount).toFixed(2)}`;
                        // Append div and amount to the list item
                        li.appendChild(div);
                        li.appendChild(amount);
                        // Append the list item to the target UL
                        recent_transactions.appendChild(li);
                    });
                }

                function createBudgetDiv(category, percentage) {
                    const div = document.createElement('div');
                    const header = document.createElement('div');
                    header.className = 'flex justify-between items-center mb-1';
                    const categorySpan = document.createElement('span');
                    categorySpan.textContent = category;
                    const percentageSpan = document.createElement('span');
                    percentageSpan.textContent = `${percentage}%`;
                    header.appendChild(categorySpan);
                    header.appendChild(percentageSpan);
                    div.appendChild(header);
                    const budget_bar = document.createElement('div');
                    budget_bar.className = `progress w-full h-2.5 bg-gray-200 rounded-full`;
                    const progress = document.createElement('div');
                    progress.className = `h-2.5 bg-pink-600 rounded-full`;
                    progress.style.width = `${percentage}%`;
                    budget_bar.appendChild(progress);
                    div.appendChild(budget_bar);
                    // const budgets_tbl = document.getElementById('budget_overview');
                    // budgets_tbl.appendChild(div);
                    return div;
                }

                function fetchBudgetOverview() {
                    const budgets_tbl = document.getElementById('budget_overview');
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', './budget/fetch.php', true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            budgets_tbl.innerHTML = '';
                            if (response.success) {
                                if (response.budgets.length > 0) {
                                    budgets_tbl.innerHTML = '';
                                    response.budgets.forEach(budget => {
                                        const percentage = (parseFloat(budget.spent_amount) / parseFloat(budget.amount)) * 100;
                                        const div = createBudgetDiv(budget.category_name, percentage);
                                        budgets_tbl.appendChild(div);
                                    })
                                } else {
                                    const div = document.createElement('div');
                                    div.className = 'flex justify-start items-center text-gray-500';
                                    div.innerHTML = `
                                        <span class="material-icons text-gray-500 mr-2">info</span>
                                        No budgets have been made yet.
                                    `;
                                    budgets_tbl.innerHTML = '';
                                    budgets_tbl.appendChild(div);
                                }
                            }
                        }
                    };
                    xhr.send();
                }
            });
        </script>
        <?php require './components/_script.php' ?>
    <?php
    }
    $_SESSION["count"] = 2;
    ?>

</body>

</html>