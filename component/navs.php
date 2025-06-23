<style>
    :root {
        --dark-color: #000000; /* This will make your icons black */
        /* You might want to define --secondary-color here as well if it's used elsewhere, e.g.: */
        /* --secondary-color: #30DFAB; or whatever color you want for hover */
    }

    .navbar {
        display: flex;
        align-items: center;
        background-color: #30DFAB;
        padding: 12px 20px;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar .logo {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark-color);
    }

    .navbar .icons {
        display: flex;
        gap: 20px; /* Space between the navigation items */
        margin-left: auto;
    }

    /* Base styling for all navigation links (the <a> tag) */
    .navbar .icons a {
        padding: 8px 12px; /* Padding for the "button" shape */
        border-radius: 8px; /* Rounded corners for the "button" */
        text-decoration: none; /* Remove default underline */
        display: flex; /* Use flex to center the icon inside the link */
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s, transform 0.2s; /* Smooth transitions */
    }

    .navbar .icons i {
        font-size: 22px;
        color: var(--dark-color); /* Default icon color (black) */
        transition: transform 0.2s, color 0.2s; /* Smooth transitions for icon */
    }

    /* Hover state for the entire link */
    .navbar .icons a:hover {
        background-color: rgba(255, 255, 255, 0.2); /* Subtle white background on hover */
    }

    /* Hover state for the icon specifically */
    .navbar .icons i:hover {
        /* If you have a --secondary-color defined, you can use it here */
        /* color: var(--secondary-color); */
        /* If you want it to remain black on hover, remove or set to black */
        color: #000000; /* Keeping it black on hover too, if that's desired */
        transform: translateY(-2px); /* Icon moves up slightly on hover */
    }

    /* Active state for the navigation link (the <a> tag) */
    .navbar .icons a.active {
        background-color: #ffffff; /* White background for the active item */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: subtle shadow */
        /* No transform here to keep it stable */
    }

    /* Active state for the icon within the active link */
    .navbar .icons a.active i {
        color: #000000; /* Ensure active icon is black */
        transform: none; /* Remove any hover transform on active icon */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar .icons {
            gap: 15px;
        }

        .navbar .icons i {
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        .navbar {
            padding: 10px 15px;
        }

        .navbar .logo {
            font-size: 20px;
        }

        .navbar .icons {
            gap: 12px;
        }

        .navbar .icons i {
            font-size: 18px;
        }
    }
</style>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- nast/?code -->
<nav class="navbar">
    <div class="logo">BandMate</div>
    <div class="icons">
        <a href="dashboard.php" class="<?php echo ($currentPage === 'dashboard.php') ? 'active' : ''; ?>"><i class="fa-solid fa-newspaper"></i></a>
        <a href="search.php" class="<?php echo ($currentPage === 'search.php') ? 'active' : ''; ?>"><i class="fa-solid fa-search"></i></a>
        <a href="contact.php" class="<?php echo ($currentPage === 'contact.php') ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i></a>
        <a href="message.php" class="<?php echo ($currentPage === 'message.php') ? 'active' : ''; ?>"><i class="fa-solid fa-message"></i></a>
        <a href="profile.php" class="<?php echo ($currentPage === 'profile.php') ? 'active' : ''; ?>"><i class="fa-solid fa-user"></i></a>
    </div>
</nav>