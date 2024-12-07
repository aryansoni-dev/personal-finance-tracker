<?php
session_start();

require __DIR__ . "/../config/db.php";
require __DIR__ . "/../classes/Category.php";

$db = getDBConnection();
if (!$db) {
    throw new Exception("Database connection failed");
}

$category = new Category($db);
$categories = $category->read();

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
    <title>FineTrack - Transaction History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link rel="icon" href="/financeTracker/favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php require './components/_styles.php' ?>
</head>

<body>
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
    ?>
        <?php require './components/_nav.php' ?>

        <div class="flex">
            <?php require './components/_aside.php' ?>

            <main class="main-content flex-1 p-6">
                <div class="bg-white rounded-lg shadow-md p-6" data-aos="fade-up">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Transaction History</h2>
                        <div class="flex space-x-4">
                            <select id="transactionFilter" class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="all">All Transactions</option>
                                <option value="income">Income Only</option>
                                <option value="expense">Expenses Only</option>
                            </select>
                            <button id="addTransactionBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none">Add New</button>
                            <button id="cancelTransactionBtn" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 focus:outline-none hidden">Cancel</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="transactionTable">
                                <!-- Transaction rows will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Transaction Form -->
                <div id="addTransactionForm" class="hidden bg-white border border-gray-200 rounded-lg shadow-md p-6 mb-6 mt-8" data-aos="fade-up">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Transaction</h3>
                    <form id="transactionForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="transactionDate" class="block text-sm font-medium text-gray-600">Date</label>
                                <input type="date" id="transactionDate" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="transactionAmount" class="block text-sm font-medium text-gray-600">Amount</label>
                                <input type="number" step="0.01" id="transactionAmount" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="transactionCategory" class="block text-sm font-medium text-gray-600">Category</label>
                                <select id="transactionCategory" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <?php
                                    foreach ($categories as $category) {
                                        echo '<option value="' . $category["id"] . '">' . $category["name"] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="transactionType" class="block text-sm font-medium text-gray-600">Type</label>
                                <select id="transactionType" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="transactionDescription" class="block text-sm font-medium text-gray-600">Description</label>
                            <textarea id="transactionDescription" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none">Save</button>
                            <button type="reset" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 focus:outline-none">Reset</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>

        <script>
            const aside_dashboard = document.getElementById('transactions-a');
            const nav_dashboard = document.getElementById('nav-transactions-a');
            let transactions = [];

            document.addEventListener('DOMContentLoaded', () => {
                aside_dashboard.classList.add('aside-a-bg', 'text-blue-500', 'translate-x-2');
                nav_dashboard.classList.add('nav-a-bg', 'text-gray-800', 'translate-x-2');

                // Sample transactions data
                fetchTransactions();
                // console.log("Transactions : ", transactions);

                // Transaction filter change handler
                document.getElementById('transactionFilter').addEventListener('change', function(e) {
                    const filteredTransactions = filterTransactions(transactions, e.target.value);
                    updateTransactionTable(filteredTransactions);
                });

                const addTransactionBtn = document.getElementById('addTransactionBtn');
                const cancelTransactionBtn = document.getElementById('cancelTransactionBtn');
                const addTransactionForm = document.getElementById('addTransactionForm');

                // Toggle Add Transaction Form
                addTransactionBtn.addEventListener('click', () => {
                    addTransactionForm.classList.remove('hidden');
                    addTransactionBtn.classList.add('hidden');
                    cancelTransactionBtn.classList.remove('hidden');
                });

                cancelTransactionBtn.addEventListener('click', () => {
                    addTransactionForm.classList.add('hidden');
                    addTransactionBtn.classList.remove('hidden');
                    cancelTransactionBtn.classList.add('hidden');
                });

                // Handle Form Submission
                document.getElementById('transactionForm').addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const date = document.getElementById('transactionDate').value;
                    const amount = parseFloat(document.getElementById('transactionAmount').value);
                    const description = document.getElementById('transactionDescription').value.trim();
                    const category = document.getElementById('transactionCategory').value.trim();
                    const type = document.getElementById('transactionType').value;

                    // Basic validation
                    if (!date || isNaN(amount) || !description || !category || !type) {
                        Swal.fire('Error', 'Please fill out all fields correctly.', 'error');
                        return;
                    }

                    try {
                        addNewTransaction(category, amount, description, date);
                    } catch (error) {
                        Swal.fire('Error', 'An error occurred while adding the transaction.', 'error');
                        console.error('Error:', error);
                    }
                });



            });

            function updateTransactionTable(transactions) {
                const tableBody = document.getElementById('transactionTable');
                tableBody.innerHTML = ''; // Clear existing rows

                if (transactions.length === 0 || !transactions) {
                    const row = document.createElement('tr');
                    row.innerHTML = row.innerHTML = `<td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex items-center justify-center">
                        <span class="material-icons text-gray-500 mr-2">info</span>
                        <span>No transactions have been made yet.</span>
                        </div>
                    </td>`;
                    tableBody.appendChild(row);
                    return;
                }

                transactions.forEach(transaction => {
                    const amount = parseFloat(transaction.amount);
                    const row = document.createElement('tr');
                    row.className = 'transaction_row';
                    row.setAttribute('data-id', transaction.id);
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">${transaction.date}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${transaction.description}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${transaction.category_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap ${transaction.category_type === 'income' ? 'text-green-600' : 'text-red-600'}">
                            ${transaction.category_type === 'income' ? '+' : '-'}${amount.toFixed(2)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                ${transaction.category_type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${transaction.category_type}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap flex items-center justify-between gap-2 md:gap-2 lg:gap-1 lg:justify-evenly">
                        <span class="edit_transaction material-icons text-blue-500 hover:text-blue-600 hover:bg-blue-200 cursor-pointer p-1 bg-blue-100 rounded-md">edit</span>
                        <span class="del_transaction material-icons text-red-500 hover:text-red-600 hover:bg-red-200 cursor-pointer p-1 bg-red-100 rounded-md">delete</span>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                // Attach event listeners dynamically for the delete and edit icons
                const deleteIcons = document.querySelectorAll('.del_transaction');
                const editIcons = document.querySelectorAll('.edit_transaction');

                deleteIcons.forEach(icon => {
                    icon.addEventListener('click', deleteTransaction, false);
                });

                editIcons.forEach(icon => {
                    icon.addEventListener('click', editTransaction, false);
                });
            }

            function fetchTransactions() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', './transaction/fetch.php', true);

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            try {
                                // Parse the server response
                                const response = JSON.parse(xhr.responseText);

                                // Check if the response has the expected structure
                                if (response.status === 'success' && Array.isArray(response.data)) {
                                    transactions = response.data;
                                    // Sort transactions by ID (or any other field you prefer)
                                    transactions.sort((a, b) => b.id - a.id);
                                    // Update the table with the new transactions
                                    updateTransactionTable(transactions);
                                } else {
                                    updateTransactionTable([]); // Pass data array to the table function
                                    // Swal.fire({
                                    //     icon: 'info',
                                    //     title: 'No Transactions Found',
                                    //     text: 'The server returned no transactions to display.',
                                    // });
                                }
                            } catch (error) {
                                // Catch JSON parse errors or unexpected structure
                                // Swal.fire({
                                //     icon: 'error',
                                //     title: 'Error',
                                //     text: 'Invalid response from the server. Please check the server logs.',
                                // });
                                updateTransactionTable([]); // Pass data array to the table function
                                console.error('Error parsing server response:', error);
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

                xhr.send(); // No payload required for a GET request
            }

            function addNewTransaction(category, amount, desc, date) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', './transaction/create.php', true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) { // Ensure request is complete
                        if (xhr.status === 200) {
                            try {
                                // console.log(xhr.responseText); // Debugging: log raw response
                                const response = JSON.parse(xhr.responseText); // Parse JSON response
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.message || 'Transaction added successfully!'
                                    }).then(() => {
                                        window.location.reload(); // Reload the page
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || 'Something went wrong, please try again...'
                                    });
                                }
                            } catch (e) {
                                console.error('Parsing error:', e); // Debugging: log error details
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Invalid response from server'
                                });
                            }
                        } else {
                            console.error('Request failed:', xhr.status, xhr.statusText); // Debugging
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Could not add transaction'
                            });
                        }
                    }
                };

                // Send data
                const params = `category_id=${encodeURIComponent(category)}&amount=${encodeURIComponent(amount)}&desc=${encodeURIComponent(desc)}&date=${encodeURIComponent(date)}`;
                xhr.send(params);
            }

            function filterTransactions(transactions, filter) {
                // console.log(transactions);
                if (filter === 'all') return transactions;
                return transactions.filter(t => t.category_type === filter);
            }

            function deleteTransaction(event) {
                const transactionRow = event.target.closest('tr'); // Get the closest <tr>
                const transactionId = transactionRow.getAttribute('data-id'); // Retrieve the data-id attribute

                if (!transactionId) {
                    console.error('Transaction ID is missing!');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This transaction will be permanently deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                }).then(result => {
                    if (result.isConfirmed) {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', './transaction/delete.php', true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                if (xhr.status === 200 && xhr.responseText.trim() === "deletion successful") {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: 'Transaction successfully deleted!'
                                    }).then(() => {
                                        // Optionally remove the row from the table without reloading
                                        fetchTransactions();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to delete the transaction',
                                    });
                                }
                            } else if (xhr.readyState === XMLHttpRequest.DONE) {
                                console.error('Request failed:', xhr.status, xhr.statusText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete the transaction',
                                });
                            }
                        };
                        // Correct parameter name
                        const params = `transactionID=${encodeURIComponent(transactionId)}`;
                        xhr.send(params);
                    }
                });
            }

            function editTransaction(transactionId) {
                console.log(`Edit transaction with ID: ${transactionId}`);
                // Add logic to handle the editing
            }
        </script>
        <?php require './components/_script.php' ?>
    <?php
    }
    ?>

</body>

</html>