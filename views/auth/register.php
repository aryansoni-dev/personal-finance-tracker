<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>FineTrack - Sign Up</title>
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
    if (isset($_SESSION["isRegistered"]) && $_SESSION["isRegistered"] == false) {
        unset($_SESSION["isRegistered"]);
    ?>
        <script>
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'Registration Failed!',
                text: 'Please try Again...',
                timer: '3000',
                showConfirmButton: false,
            });
        </script>
    <?php
    }
    ?>
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Sign Up for FineTrack</h2>
            </div>
            <form action="./proceedRegistration.php" method="post">
                <div class="mb-4 relative">
                    <span class="material-icons absolute left-3 top-2 text-pink">person</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="username" name="username" type="text" placeholder="Username">
                </div>
                <div class="mb-4 relative">
                    <span class="material-icons absolute left-3 top-2 text-blue">person</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="name" name="name" type="text" placeholder="Full Name">
                </div>
                <div class="mb-4 relative">
                    <span class="material-icons absolute left-2 top-2 text-pink">email</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="email" name="email" type="email" placeholder="Email">
                </div>
                <div class="mb-4 relative">
                    <span class="material-icons absolute left-2 top-2 text-blue">lock</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="passwd" name="passwd" type="password" placeholder="Password (Atleast 8 characters long)">
                </div>
                <div class="mb-6 relative">
                    <span class="material-icons absolute left-2 top-2 text-pink">lock</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="confirm_passwd" type="password" placeholder="Confirm Password">
                </div>
                <div class="flex items-center justify-center">
                    <button id="btn_signup"
                        name="btn_signup"
                        class="gradient-bg hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hover-scale"
                        type="submit">
                        Sign Up
                    </button>
                </div>
            </form>
        </div>
        <p class="text-center text-gray-500 text-sm mb-2">
            Already have an account? <a href="./login.php" class="text-blue-500 hover:text-blue-800 text_semiBold">Log
                in</a>
        </p>
        <p class="text-center text-gray-500 text-xs mt-2">
            &copy;2024 FineTrack. All rights reserved.
        </p>
    </div>

    <script>
        let isUsernameValid = false;
        let isNameValid = false;
        let isEmailValid = false;
        let isPasswordValid = false;
        let isConfirmPasswordValid = false;

        const input_name = document.querySelector('#name');
        const input_username = document.querySelector('#username');
        const input_email = document.querySelector('#email');
        const input_passwd = document.querySelector('#passwd');
        const input_confirm_passwd = document.querySelector('#confirm_passwd');
        const btn_signup = document.querySelector('#btn_signup');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validate name (at least 5 characters)
        input_name.addEventListener('input', () => {
            if (input_name.value === "" || input_name.value.length < 5) {
                input_name.style.border = "2px solid #ff3552";
                isNameValid = false;
            } else {
                input_name.style.border = "2px solid #4eff77";
                isNameValid = true;
            }
        });

        input_username.addEventListener('input', () => {
            if (input_username.value === "" || input_username.value.length < 5) {
                input_username.style.border = "2px solid #ff3552";
                isNameValid = false;
            } else {
                input_username.style.border = "2px solid #4eff77";
                isNameValid = true;
            }
        });

        // Validate email with regex
        input_email.addEventListener('input', () => {
            if (!emailPattern.test(input_email.value)) {
                input_email.style.border = "2px solid #ff3552";
                isEmailValid = false;
            } else {
                input_email.style.border = "2px solid #4eff77";
                isEmailValid = true;
            }
        });

        // Validate password (at least 8 characters)
        input_passwd.addEventListener('input', () => {
            if (input_passwd.value === "" || input_passwd.value.length < 8) {
                input_passwd.style.border = "2px solid #ff3552";
                isPasswordValid = false;
            } else {
                input_passwd.style.border = "2px solid #4eff77";
                isPasswordValid = true;
            }
        });

        // Validate confirm password (must match password)
        input_confirm_passwd.addEventListener('input', () => {
            if (input_confirm_passwd.value === "" || input_confirm_passwd.value !== input_passwd.value) {
                input_confirm_passwd.style.border = "2px solid #ff3552";
                isConfirmPasswordValid = false;
            } else {
                input_confirm_passwd.style.border = "2px solid #4eff77";
                isConfirmPasswordValid = true;
            }
        });

        // Validate the entire form when clicking the sign-up button
        function validate(event) {
            // Check if all fields are valid
            if (!isNameValid || !isEmailValid || !isPasswordValid || !isConfirmPasswordValid) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: "error",
                    title: "Error...",
                    text: "Please fill out the form correctly!",
                });
            }
            if (input_name.value === "" || input_name.value.length < 5) {
                input_name.style.border = "2px solid #ff3552";
                isNameValid = false;
            } else {
                input_name.style.border = "2px solid #4eff77";
                isNameValid = true;
            }
            if (!emailPattern.test(input_email.value)) {
                input_email.style.border = "2px solid #ff3552";
                isEmailValid = false;
            } else {
                input_email.style.border = "2px solid #4eff77";
                isEmailValid = true;
            }
            if (input_passwd.value === "" || input_passwd.value.length < 8) {
                input_passwd.style.border = "2px solid #ff3552";
                isPasswordValid = false;
            } else {
                input_passwd.style.border = "2px solid #4eff77";
                isPasswordValid = true;
            }
            if (input_confirm_passwd.value === "" || input_confirm_passwd.value !== input_passwd.value) {
                input_confirm_passwd.style.border = "2px solid #ff3552";
                isConfirmPasswordValid = false;
            } else {
                input_confirm_passwd.style.border = "2px solid #4eff77";
                isConfirmPasswordValid = true;
            }
            register();
        }
        btn_signup.addEventListener('click', validate);

        // function register() {
        //     const name = input_name.value.trim();
        //     const username = input_username.value.trim();
        //     const email = input_email.value.trim();
        //     const passwd = input_passwd.value.trim();
        //     const confirm_passwd = input_confirm_passwd.value.trim();

        //     // Validate the input fields
        //     if (!name || !username || !email || !passwd || !confirm_passwd || passwd !== confirm_passwd) {
        //         Swal.fire({
        //             icon: "error",
        //             title: "Error...",
        //             text: "Please fill out the form correctly!",
        //         });
        //         return;
        //     }

        //     // Create a new XMLHttpRequest
        //     const xhr = new XMLHttpRequest();
        //     xhr.open('POST', './proceedRegistration.php', true);
        //     xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        //     xhr.onreadystatechange = function() {
        //         if (xhr.readyState === XMLHttpRequest.DONE) {
        //             if (xhr.status === 200) {
        //                 const response = xhr.responseText.trim();
        //                 if (response === "Registration successful!") {
        //                     Swal.fire({
        //                         icon: "success",
        //                         title: "Registration successful!",
        //                         text: "You can now log in with your credentials.",
        //                         confirmButtonText: "Login",
        //                     }).then((result) => {
        //                         if (result.isConfirmed) {
        //                             window.location.href = "./login.php";
        //                         }
        //                     });
        //                 } else {
        //                     Swal.fire({
        //                         icon: "error",
        //                         title: "Error...",
        //                         text: response,
        //                     });
        //                 }
        //             } else {
        //                 Swal.fire({
        //                     icon: "error",
        //                     title: "Error...",
        //                     text: "Failed to connect to the server. Please try again later.",
        //                 });
        //             }
        //         }
        //     };

        //     // Properly format the parameters
        //     const params = `username=${encodeURIComponent(username)}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&passwd=${encodeURIComponent(passwd)}`;

        //     // Send the request with the parameters
        //     xhr.send(params);
        // }
    </script>
</body>

</html>