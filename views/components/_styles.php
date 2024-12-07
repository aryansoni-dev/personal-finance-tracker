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

    .sidebar {
        height: 100vh;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        position: sticky;
        top: 0;
        align-self: start;
    }

    .logout {
        position: absolute;
        bottom: 5rem;
    }

    .logo {
        color: #fff;
        border-radius: 50%; /* Makes the logo circular */
        width: 60px;      /* Fixed width for the logo */
        height: 60px;     /* Fixed height to ensure a perfect circle */
        object-fit: cover; /* Ensures the content fits within the circular shape */
        border: 2px solid #4e6eff; /* Optional: Adds a colored border for styling */
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Adds a subtle shadow */
    }

    /* Mobile menu styles */
    .mobile-menu {
        margin-top: 0.1rem;
        display: none;
        background: linear-gradient(90deg, #4e6eff 0%, #ff5ae9 100%);
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 50;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
    }

    .mobile-menu.active {
        display: block;
        animation: slideDown 0.3s ease-in-out forwards;
    }

    @keyframes slideDown {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
        }

        .main-content {
            margin-left: 0;
        }
    }

    .aside-a-bg {
        border-left-width: 4px;
        border-left-color: rgb(59 130 246);
        background-color: rgb(226 232 240);
    }

    .nav-a-bg {
        background: rgba(0, 0, 0, 0.2);
    }
</style>