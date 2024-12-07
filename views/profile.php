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
    <title>FineTrack - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link rel="icon" href="/financeTracker/favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php require 'components/_styles.php' ?>
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
        require_once __DIR__ . '/../config/db.php';
        require_once __DIR__ . '/../classes/User.php';

        $db = getDBConnection();
        if (!$db) {
            throw new Exception("Database connection failed");
        }

        $user = new User($db);
        $user_data = $user->getUserById($_SESSION['userID']);

        if (!$user_data) {
            throw new Exception("Failed to fetch user data");
        }
        // echo json_encode($user_data);
        $name = htmlspecialchars($user_data['name'] ?? 'N/A');
        $username = htmlspecialchars($user_data['username'] ?? 'N/A');
        $email = htmlspecialchars($user_data['email'] ?? 'N/A');
        $joined = htmlspecialchars(date('F d, Y', strtotime($user_data['created_at'] ?? 'now')));
    ?>
        <?php require 'components/_nav.php' ?>

        <div class="flex">
            <?php require './components/_aside.php' ?>

            <main class="main-content flex-1 p-6">
                <h1 class="text-3xl font-bold mb-6">Profile</h1>

                <div class="bg-white p-6 rounded-lg shadow-md hover-scale" data-aos="fade-up">
                    <form id="profile-form" action="#" method="POST">
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-gray-100 cursor-not-allowed rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo $username; ?>" disabled>
                        </div>

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo $name; ?>" disabled>
                        </div>

                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-gray-100 cursor-not-allowed rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo $email; ?>" disabled>
                        </div>

                        <div class="mb-6">
                            <label for="joined" class="block text-sm font-medium text-gray-700">Joined Since</label>
                            <input type="text" id="joined" name="joined" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-gray-100 cursor-not-allowed rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo $joined; ?>" disabled>
                        </div>

                        <div class="flex justify-between">
                            <div class="flex justify-between items-center gap-3">
                                <button id="btn_reset_passwd" type="button" class="flex items-center gap-2 bg-blue-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span class="material-icons text-base">refresh</span>
                                    <span class="text-base">Reset Password</span>
                                </button>
                                <button id="btn_del_acc" type="button" class="flex items-center gap-2 bg-red-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <span class="material-icons text-base">delete</span>
                                    <span class="text-base">Delete Account</span>
                                </button>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <button type="button" id="edit-btn" class="flex items-center gap-2 bg-gray-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    <span class="material-icons text-base">edit</span>
                                    <span class="text-base">Edit</span>
                                </button>
                                <button type="button" id="update-profile-btn" name="update-profile-btn" class="flex items-center gap-2 bg-green-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 hidden">
                                    <span class="material-icons text-base">check</span>
                                    <span class="text-base">Save Changes</span>
                                </button>
                                <button type="button" id="cancel-update-profile-btn" class="flex items-center gap-2 bg-red-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 hidden">
                                    <span class="material-icons text-base">close</span>
                                    <span class="text-base">Cancel</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>

        <?php require 'components/_script.php' ?>
        <script>
            const profile = document.getElementById('profile-a');
            const nav_profile = document.getElementById('nav-profile-a');
            document.addEventListener('DOMContentLoaded', () => {
                profile.classList.add('aside-a-bg', 'text-blue-500', 'translate-x-2');
                nav_profile.classList.add('nav-a-bg', 'text-gray-800', 'translate-x-2');

                const saveBtn = document.getElementById('update-profile-btn');
                if (saveBtn) {
                    saveBtn.addEventListener('click', updateUserProfile, false);
                }

                const btn_reset_passwd = document.getElementById('btn_reset_passwd');
                if (btn_reset_passwd) {
                    btn_reset_passwd.addEventListener('click', () => {
                        window.location.href = "/financeTracker/views/resetPasswd.php";
                    });
                }

                const btn_del_acc = document.getElementById('btn_del_acc');
                if (btn_del_acc) {
                    btn_del_acc.addEventListener('click', deleteUserAccount, false);
                }


            });

            function deleteUserAccount() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                        const xhr = new XMLHttpRequest();
                        xhr.open("DELETE", "/financeTracker/views/profile/deleteUserAccount.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                Swal.close(); // Close the loading indicator

                                if (xhr.status === 200) {
                                    try {
                                        const response = JSON.parse(xhr.responseText);
                                        if (response.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Deletion Successful!',
                                                text: response.message,
                                            }).then(() => {
                                                window.location.href = "/financeTracker/views/auth/register.php";
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Deletion Failed!',
                                                text: response.message,
                                            });
                                        }
                                    } catch (err) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'An error occurred!',
                                            text: 'Invalid server response!',
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Request Failed!',
                                        text: `Server returned status: ${xhr.status}`,
                                    });
                                }
                            }
                        };

                        xhr.send();
                    }
                });
            }

            function updateUserProfile() {
                const username = document.getElementById('username').value.trim();
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "/financeTracker/views/profile/updateUserProfile.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updation Successful!',
                                    text: response.message,
                                }).then(() => {
                                    // Optionally reload the page or update UI
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Updation Failed!',
                                    text: response.message,
                                });
                            }
                        } catch (err) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid server response',
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Request Failed',
                            text: 'Unable to update profile',
                        });
                    }
                };
                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Please check your internet connection.',
                    });
                };
                // Prepare data to send
                const data = `username=${encodeURIComponent(username)}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`;
                xhr.send(data);
            }
        </script>
    <?php
    }
    ?>

</body>

</html>