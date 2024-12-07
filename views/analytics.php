<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>FineTrack - Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link rel="icon" href="/financeTracker/favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    ?>
        <?php require './components/_nav.php' ?>

        <div class="flex">
            <?php require './components/_aside.php' ?>

            <main class="main-content flex-1 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Analytics</h1>
                    <select id="timeRange" class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <!-- <option value="today">Today</option> -->
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 mb-6" data-aos="fade-up">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Income vs Expenses</h2>
                        <select id="chartType" class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="bar">Bar Chart</option>
                            <option value="line">Line Chart</option>
                        </select>
                    </div>
                    <div class="h-[400px]">
                        <canvas id="incomeExpensesChart"></canvas>
                    </div>
                </div>
            </main>
        </div>

        <?php require './components/_script.php' ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize AOS
                AOS.init({
                    duration: 1000,
                    once: true,
                });

                const aside_analytics = document.getElementById('analytics-a');
                const nav_analytics = document.getElementById('nav-analytics-a');
                aside_analytics.classList.add('aside-a-bg', 'text-blue-500', 'translate-x-2');
                nav_analytics.classList.add('nav-a-bg', 'text-gray-800', 'translate-x-2');

                // Initialize chart container and config
                const ctx = document.getElementById('incomeExpensesChart').getContext('2d');
                let currentChart = null;

                // Fetch transactions and update chart
                fetchTransactions();

                // Chart type change handler
                document.getElementById('chartType').addEventListener('change', function(e) {
                    if (currentChart) {
                        currentChart.destroy();
                    }
                    currentChart = new Chart(ctx, createChartConfig(e.target.value));
                });

                function fetchTransactions() {
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', './transaction/fetch.php', true);

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                try {
                                    const response = JSON.parse(xhr.responseText);

                                    if (response.status === 'success' && Array.isArray(response.data)) {
                                        // console.log(response.data);
                                        // Calculate income and expenses totals
                                        let income = 0;
                                        let expenses = 0;

                                        response.data.forEach(transaction => {
                                            if (transaction.category_type === 'income') {
                                                income += parseFloat(transaction.amount);
                                            } else if (transaction.category_type === 'expense') {
                                                expenses += parseFloat(transaction.amount);
                                            }
                                        });
                                        // Update chart with the calculated totals
                                        const chartData = {
                                            labels: ['Total'],
                                            income: [income],
                                            expenses: [expenses]
                                        };
                                        // If chart already exists, destroy it
                                        if (currentChart) {
                                            currentChart.destroy();
                                        }
                                        // Create a new chart with dynamic data
                                        currentChart = new Chart(ctx, createChartConfig('bar', chartData));
                                    } else {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'No Transactions Found',
                                            text: 'The server returned no transactions to display.',
                                        });
                                    }
                                } catch (error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Invalid response from the server. Please check the server logs.',
                                    });
                                    console.error('Error parsing server response:', error);
                                }
                            } else {
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

                function createChartConfig(type, data = {
                    income: [],
                    expenses: []
                }) {
                    return {
                        type: type,
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Income',
                                    data: data.income,
                                    backgroundColor: 'rgba(78, 110, 255, 0.5)',
                                    borderColor: '#4e6eff',
                                    borderWidth: 2
                                },
                                {
                                    label: 'Expenses',
                                    data: data.expenses,
                                    backgroundColor: 'rgba(255, 90, 233, 0.5)',
                                    borderColor: '#ff5ae9',
                                    borderWidth: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    };
                }
            });
        </script>
    <?php
    }
    ?>

</body>

</html>