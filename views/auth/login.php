<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_signIn'])) {
    try {
        $username = trim($_POST['username']);
        $passwd = trim($_POST['passwd']);

        if (empty($username) || empty($passwd)) {
            throw new Exception("All fields are required");
        }

        $db = getDBConnection();
        if (!$db) {
            throw new Exception("Database connection failed");
        }

        $user = new User($db);
        $result = $user->login($username, $passwd);

        if ($result) {
            $_SESSION["isAuthenticated"] = true;
            $_SESSION["userID"] = $result;
            $_SESSION["count"] = 1;
            header("Location: ../dashboard.php");
            exit();
        } else {
            $_SESSION["isAuthenticated"] = false;
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0"> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>FineTrack - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="/financeTracker/favicon/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(90deg, #4e6eff 0%, #ff5ae9 100%);
        }

        .hover-scale {
            transition: transform 0.3s ease-in-out;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        .text_semiBold {
            font-weight: 600;
        }

        .text-blue {
            color: #4e6eff;
        }

        .text-pink {
            color: #ff5ae9;
        }
    </style>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">
    <?php
    if (isset($_SESSION["isRegistered"]) && $_SESSION["isRegistered"] == true) {
        unset($_SESSION["isRegistered"]);
    ?>
        <script>
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Registration Successful!',
                text: 'You can now login...',
                timer: '3000',
                showConfirmButton: false,
            });
        </script>
    <?php
    } else if (isset($_SESSION["isAuthenticated"]) && $_SESSION["isAuthenticated"] == false) {
    ?>
        <script>
            Swal.fire({
                // position: 'top-end',
                icon: 'error',
                title: 'Login Failed!',
                text: 'Please try again...',
                timer: '3000',
                // showConfirmButton: false,
            });
        </script>
    <?php
    }
    ?>
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Login to FineTrack</h2>
            </div>
            <form action="./login.php" method="POST">
                <div class="mb-4 relative">
                    <span class="material-icons absolute left-2 top-2 text-blue">person</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="username" name="username" type="text" placeholder="Username" value="" autocomplete="off">
                </div>
                <div class="mb-6 relative">
                    <span class="material-icons absolute left-2 top-2 text-pink">lock</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                        id="passwd" name="passwd" type="password" placeholder="Password (Atleast 8 characters long)" value="">
                </div>
                <div class="flex items-center justify-between">
                    <button id="btn_signIn"
                        name="btn_signIn"
                        class="gradient-bg hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hover-scale"
                        type="submit">
                        Sign In
                    </button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                        href="../resetPasswd.php">
                        Forgot Password?
                    </a>
                </div>
            </form>
        </div>
        <p class="text-center text-gray-500 text-sm mb-2">
            Don't have an account? <a href="./register.php" class="text-blue-500 hover:text-blue-800 text_semiBold">Register</a>
        </p>
        <p class="text-center text-gray-500 text-xs">
            &copy;2024 FineTrack. All rights reserved.
        </p>
    </div>

    <script>
        let isNameValid = false;
        let isPasswordValid = false;

        const input_name = document.querySelector('#username');
        const input_passwd = document.querySelector('#passwd');
        const btn_signIn = document.querySelector('#btn_signIn');
        // const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validate email with regex on input
        input_name.addEventListener('input', () => {
            if (input_name.value === "" || input_name.value.length < 5) {
                input_name.style.border = "2px solid #ff3552";
                isNameValid = false;
            } else {
                input_name.style.border = "2px solid #4eff77";
                isNameValid = true;
            }
        });

        // Validate password (at least 8 characters) on input
        input_passwd.addEventListener('input', () => {
            if (input_passwd.value === "" || input_passwd.value.length < 8) {
                input_passwd.style.border = "2px solid #ff3552";
                isPasswordValid = false;
            } else {
                input_passwd.style.border = "2px solid #4eff77";
                isPasswordValid = true;
            }
        });

        // Function to validate form when submit button is clicked
        function validate(event) {
            // Check if both fields are valid
            if (!isNameValid || !isPasswordValid) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: "error",
                    title: "Error...",
                    text: "Please fill out the form correctly!",
                });
            }
        }

        // Add the click event listener for form validation
        btn_signIn.addEventListener('click', validate);
    </script>
</body>

</html>