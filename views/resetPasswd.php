<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FineTrack - Reset Password</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="/financeTracker/favicon/favicon.ico" type="image/x-icon">
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

        .text-blue {
            color: #4e6eff;
        }

        .text-pink {
            color: #ff5ae9;
        }
    </style>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Reset Password</h2>
                <p class="text-gray-600 mt-2">Enter your email to receive a password reset link</p>
            </div>
            <form action="#">
                <div class="mb-6 relative">
                    <span class="material-icons absolute left-2 top-2 text-blue">email</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="email" name="email" type="email" placeholder="Email">
                </div>
                <div class="flex items-center justify-center">
                    <button
                        class="gradient-bg hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hover-scale w-half cursor-not-allowed"
                        type="button"
                        id="send-link-btn" disabled>
                        Send Reset Link
                    </button>
                </div>
            </form>
            <div class="mt-6 text-center">
                <a href="./auth/login.php" class="font-bold text-sm text-blue-500 hover:text-blue-800">
                    Back to Login
                </a>
            </div>
        </div>
        <p class="text-center text-gray-500 text-xs">
            &copy;2024 FineTrack. All rights reserved.
        </p>
    </div>

    <script>
        function validate(event) {
            // Check if both fields are valid
            if (!isEmailValid) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: "error",
                    title: "Error...",
                    text: "Please provide a valid email!",
                });
            }
        }

        function sendEmail() {
            const email = document.getElementById('email').value;
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../mailer/ProcessResetRequest.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText === 'Sent') {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Password reset link sent to your email."
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: xhr.responseText
                        });
                    }
                }
            };

            xhr.send(`email=${encodeURIComponent(email)}`);
        }

        window.addEventListener('DOMContentLoaded', () => {
            let isEmailValid = false;
            const input_email = document.querySelector('#email');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const sendEmailBtn = document.getElementById("send-link-btn");

            // Validate email with regex on input
            input_email.addEventListener('input', () => {
                if (!emailPattern.test(input_email.value)) {
                    input_email.style.border = "2px solid #ff3552";
                    isEmailValid = false;
                    sendEmailBtn.disabled = true;
                    sendEmailBtn.classList.add("cursor-not-allowed");
                } else {
                    input_email.style.border = "2px solid #4eff77";
                    isEmailValid = true;
                    sendEmailBtn.disabled = false;
                    sendEmailBtn.classList.remove("cursor-not-allowed");
                }
            });

            if (sendEmailBtn) {
                sendEmailBtn.addEventListener("click", sendEmail, false);
            }

        });
    </script>
</body>

</html>