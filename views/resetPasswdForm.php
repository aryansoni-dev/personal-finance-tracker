<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FineTrack - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <h2 class="text-3xl font-bold text-gray-800">FineTrack - Reset Password</h2>
            </div>
            <form id="password-reset-form" action="#">
                <input type="hidden" id="token" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <div class="mb-4 relative">
                    <span class="material-icons absolute left-2 top-2 text-blue">lock</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="passwd" name="passwd" type="password" placeholder="New Password (At least 8 characters)" required>
                </div>
                <div class="mb-6 relative">
                    <span class="material-icons absolute left-2 top-2 text-pink">lock</span>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 pl-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="confirm_passwd" type="password" placeholder="Confirm New Password" required>
                </div>
                <div class="flex items-center justify-center">
                    <button type="button" id="btn_reset"
                        class="gradient-bg hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hover-scale">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
        <p class="text-center text-gray-500 text-xs">&copy;2024 FineTrack. All rights reserved.</p>
    </div>

    <script>
        const passwd = document.getElementById('passwd');
        const confirm_passwd = document.getElementById('confirm_passwd');
        const token = document.getElementById('token').value;

        passwd.addEventListener('input', () => {
            if (passwd.value.length < 8) {
                passwd.style.border = "2px solid #ff3552";
            } else {
                passwd.style.border = "2px solid #4eff77";
            }
        });

        confirm_passwd.addEventListener('input', () => {
            if (confirm_passwd.value !== passwd.value) {
                confirm_passwd.style.border = "2px solid #ff3552";
            } else {
                confirm_passwd.style.border = "2px solid #4eff77";
            }
        });

        document.getElementById('btn_reset').addEventListener('click', () => {
            if (!passwd.value || passwd.value.length < 8 || passwd.value !== confirm_passwd.value) {
                passwd.style.border = "2px solid #ff3552";
                confirm_passwd.style.border = "2px solid #ff3552";
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Passwords do not match or are too short."
                });
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "./ProcessResetPasswd.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200 && xhr.responseText === "Password has been reset!") {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: xhr.responseText
                    }).then(() => {
                        window.location.href = "./auth/login.php";
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: xhr.responseText
                    });
                }
            };

            xhr.send(`token=${encodeURIComponent(token)}&passwd=${encodeURIComponent(passwd.value)}`);
        });
    </script>
</body>

</html>