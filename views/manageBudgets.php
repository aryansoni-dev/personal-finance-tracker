<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../classes/Total.php";
require __DIR__ . "/../classes/Category.php";

$db = getDBConnection();
if (!$db) {
    throw new Exception("Database connection failed");
}

$total = new Total($db);
$balance = $total->getWalletBalance($_SESSION['userID']);
$totalIncome = $total->getTransactionTotalByType($_SESSION['userID'], "income");
$totalExpenses = $total->getTransactionTotalByType($_SESSION['userID'], "expense");
$savings = $totalIncome - $totalExpenses;

$category = new Category($db);
$categories = $category->read();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>FineTrack - Manage Budgets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link rel="icon" href="/financeTracker/favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php require './components/_styles.php' ?>

</head>

<body class="bg-gray-100">
    <?php require './components/_nav.php'; ?>
    <div class="flex">
        <?php require './components/_aside.php'; ?>

        <main class="main-content flex-1 p-6 space-y-8">
            <!-- Page Header -->
            <div class="flex justify-between items-center" data-aos="fade-up">
                <h1 class="text-3xl font-bold">Manage Budgets</h1>
                <!-- <button class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700" id="addBudgetButton">
                    <span class="material-icons align-middle">add</span> Add Budget
                </button> -->
            </div>

            <!-- Wallet Details -->
            <div class="bg-white p-6 rounded-lg shadow-md" data-aos="fade-up" data-aos-delay="100">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Wallet Overview</h2>
                    <button class="bg-blue-600 text-white px-2 py-1 rounded-lg shadow-md hover:bg-blue-700 flex items-center justify-between gap-1" id="updateWalletBalanceBtn">
                        <span class="material-icons align-middle text-base flex gap-2">edit</span>
                        <span class="align-middle text-base">Update Balance</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <div class="bg-blue-50 p-4 rounded-lg shadow-md text-center flex items-center justify-around">
                        <h3 class="text-lg font-medium text-blue-700">Wallet Balance</h3>
                        <p class="text-2xl font-bold text-blue-600">₹ <?php echo number_format($balance, 2); ?></p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg shadow-md text-center flex items-center justify-around">
                        <h3 class="text-lg font-medium text-green-700">Total Income</h3>
                        <p class="text-2xl font-bold text-green-600">₹ <?php echo number_format($totalIncome, 2); ?></p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg shadow-md text-center flex items-center justify-around">
                        <h3 class="text-lg font-medium text-red-700">Total Expenses</h3>
                        <p class="text-2xl font-bold text-red-600">₹ <?php echo number_format($totalExpenses, 2); ?></p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg shadow-md text-center flex items-center justify-around">
                        <h3 class="text-lg font-medium text-purple-700">Savings</h3>
                        <p class="text-2xl font-bold text-purple-600">₹ <?php echo number_format($savings, 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Budget Management -->
            <div class="bg-white p-6 rounded-lg shadow-md" data-aos="fade-up" data-aos-delay="200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold mb-4">Manage Budgets</h2>
                    <button class="bg-blue-600 text-white px-2 py-1 rounded-lg shadow-md hover:bg-blue-700 flex items-center justify-between gap-1" id="addBudgetButton">
                        <span class="material-icons align-middle text-md flex gap-1">add</span>
                        <span class="align-middle text-base">Add Budget</span>
                    </button>
                </div>
                <div class="space-y-6" id="budgets_tbl">
                    <!-- Updated Dynamically -->
                </div>
            </div>
        </main>
    </div>

    <!-- Add Budget Modal -->
    <div id="addBudgetModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold mb-4">Add New Budget</h3>
            <form id="addBudgetForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="budget_category" name="category" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        <?php
                        foreach ($categories as $category) {
                            echo '<option value="' . $category["id"] . '">' . $category["name"] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Allocation (₹)</label>
                    <input id="budget_amount" type="number" name="allocation" class="w-full p-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="bg-gray-300 px-4 py-2 rounded-lg hover:bg-gray-400" id="cancelAddBudget">Cancel</button>
                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" onclick="addBudget()">Add</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const aside_budgets = document.getElementById('budget-a');
        const nav_budgets = document.getElementById('nav-budget-a');
        // Modal functionality
        const addBudgetButton = document.getElementById('addBudgetButton');
        const addBudgetModal = document.getElementById('addBudgetModal');
        const cancelAddBudget = document.getElementById('cancelAddBudget');

        const updateWalletBalanceBtn = document.getElementById('updateWalletBalanceBtn');

        document.addEventListener('DOMContentLoaded', () => {
            aside_budgets.classList.add('aside-a-bg', 'text-blue-500', 'translate-x-2');
            nav_budgets.classList.add('nav-a-bg', 'text-gray-800', 'translate-x-2');

            fetchBudgets();

            addBudgetButton.addEventListener('click', () => {
                addBudgetModal.classList.remove('hidden');
            });
            cancelAddBudget.addEventListener('click', () => {
                addBudgetModal.classList.add('hidden');
            });
            updateWalletBalanceBtn.addEventListener('click', () => {
                updateWallet();
            });

            // Handle form submission
            const addBudgetForm = document.getElementById('addBudgetForm');
            addBudgetForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(addBudgetForm);
                // console.log(Object.fromEntries(formData)); // Replace with AJAX to save the budget
                // Swal.fire('Success!', 'Budget added successfully!', 'success');
                addBudgetModal.classList.add('hidden');
            });

        });

        function addBudget() {
            const amount = document.getElementById('budget_amount').value;
            const category = document.getElementById('budget_category').value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "./budget/add.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        Swal.fire('Success!', response.message, 'success').then(() => {
                            addBudgetModal.classList.add('hidden');
                            document.getElementById('budget_amount').value = '';
                            fetchBudgets();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            };
            const params = `amount=${encodeURIComponent(amount)}&categoryID=${encodeURIComponent(category)}`;
            xhr.send(params);
        }

        function createBudgetCard(id, category, allocated, spent) {
            // Create the main container div
            const container = document.createElement("div");
            container.className = "flex justify-between items-center bg-gray-100 p-4 rounded-lg shadow-md";

            // Create the left section (Category and allocation details)
            const leftSection = document.createElement("div");
            leftSection.className = "flex flex-col";

            const categorySpan = document.createElement("span");
            categorySpan.className = "font-medium";
            categorySpan.textContent = category;

            const allocationSpan = document.createElement("span");
            allocationSpan.className = "text-sm text-gray-500";
            allocationSpan.textContent = `Allocated: ₹ ${allocated.toLocaleString()} / Spent: ₹ ${spent.toLocaleString()}`;

            leftSection.appendChild(categorySpan);
            leftSection.appendChild(allocationSpan);

            // Create the middle section (Progress bar)
            const middleSection = document.createElement("div");
            middleSection.className = "w-full max-w-md";

            const progressBarBackground = document.createElement("div");
            progressBarBackground.className = "w-full bg-gray-300 rounded-full h-2.5";

            const progressBarForeground = document.createElement("div");
            progressBarForeground.className = "bg-pink-600 h-2.5 rounded-full md:max-w-md";
            spentPercentage = (parseFloat(spent) / parseFloat(allocated)) * 100;
            progressBarForeground.style.width = `${spentPercentage}%`;

            progressBarBackground.appendChild(progressBarForeground);
            middleSection.appendChild(progressBarBackground);

            // Create the right section (Edit and Delete icons)
            const rightSection = document.createElement("div");
            rightSection.className = "flex items-center justify-between gap-3 md:ml-3";

            const editIcon = document.createElement("span");
            editIcon.className = "material-icons text-blue-500 hover:text-blue-600 hover:bg-blue-200 cursor-pointer p-1 bg-blue-100 rounded-md";
            editIcon.textContent = "edit";
            editIcon.addEventListener("click", () => editBudget(id), false);

            const deleteIcon = document.createElement("span");
            deleteIcon.className = "material-icons text-red-500 hover:text-red-600 hover:bg-red-200 cursor-pointer p-1 bg-red-100 rounded-md";
            deleteIcon.textContent = "delete";
            deleteIcon.addEventListener("click", () => deleteBudget(id), false);

            rightSection.appendChild(editIcon);
            rightSection.appendChild(deleteIcon);

            // Append all sections to the container
            container.appendChild(leftSection);
            container.appendChild(middleSection);
            container.appendChild(rightSection);
            // console.log(id);
            // container.setAttribute('budget-id', id);
            return container;
        }

        function fetchBudgets() {
            const budgets_tbl = document.getElementById('budgets_tbl');
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "./budget/fetch.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        if (response.budgets.length > 0) {
                            budgets_tbl.innerHTML = '';
                            response.budgets.forEach(budget => {
                                const budgetCard = createBudgetCard(
                                    budget.id,
                                    budget.category_name,
                                    budget.amount,
                                    budget.spent_amount
                                );
                                budgets_tbl.appendChild(budgetCard);
                            });
                        } else {
                            // No budgets found: Append a message card
                            budgets_tbl.innerHTML = '';
                            const noBudgetCard = document.createElement('div');
                            noBudgetCard.className = "flex items-center justify-center bg-gray-100 p-6 rounded-lg shadow-md text-gray-600";
                            noBudgetCard.innerHTML = `
                                <span class="material-icons text-gray-500 mr-2">info</span>
                                No budgets have been created yet.
                            `;
                            budgets_tbl.appendChild(noBudgetCard);
                        }
                    } else {
                        console.error(response.message);
                    }
                }
            };
            xhr.send();
        }

        function deleteBudget(id) {
            // console.log(id);
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.isConfirmed) {
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "./budget/delete.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                Swal.fire('Success!', response.message, 'success').then(() => {
                                    fetchBudgets();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    };
                    const params = `budgetID=${encodeURIComponent(id)}`;
                    xhr.send(params);
                }
            });
        }

        async function editBudget(id) {
            const { value: newBudgetAmount } = await Swal.fire({
                title: "Edit Budget",
                input: "text",
                inputLabel: "New Budget Amount",
                inputPlaceholder: "Enter new amount for the budget",
                confirmButtonText: "Update",
            });
            if (newBudgetAmount) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "./budget/update.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => {
                                fetchBudgets();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                };
                const params = `budgetID=${encodeURIComponent(id)}&amount=${encodeURIComponent(newBudgetAmount)}`;
                xhr.send(params);
            } else {
                console.error('Missing new budget amount');
            }
        }

        async function updateWallet() {
            const { value: balance } = await Swal.fire({
                title: "Update Wallet Balance",
                input: "text",
                inputLabel: "New Balance Amount",
                inputPlaceholder: "Enter new amount for the wallet balance",
                confirmButtonText: "Update",
            });
            if (balance) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "./budget/updateWalletBalance.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                };
                const params = `balance=${encodeURIComponent(balance)}`;
                xhr.send(params);
            } else {
                console.error('Missing new balance amount');
            }
        }
    </script>
    <?php require "./components/_script.php"; ?>
</body>

</html>