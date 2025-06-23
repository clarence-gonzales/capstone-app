<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// You might include connectdb.php here if search.php interacts with the database
// include 'connectdb.php';

// If you have search logic, it would go here
// For example, handling search queries, fetching results, etc.
// $search_query = isset($_GET['query']) ? $_GET['query'] : '';
// $category = isset($_GET['category']) ? $_GET['category'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <title>BandMate - Search</title>
  <style>
    /* General Reset & Variables */
    :root {
        --primary-color: #30DFAB;
        --secondary-color: #2A9D8F;
        --dark-color: #264653;
        --light-color: #E9F5F2;
        --accent-color: #F4A261;
        --text-color: #333333;
        --text-light: #777777;
        --white: #FFFFFF;
        --gray-light: #F5F5F5;
        --gray-medium: #E0E0E0;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --shadow-hover: 0 6px 16px rgba(0, 0, 0, 0.12);
        --border-radius: 12px;
        --border-radius-sm: 8px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--gray-light); /* Consistent background */
        color: var(--text-color);
        line-height: 1.6;
    }

    

    /* Main content container for search.php */
    .content {
        max-width: 1200px;
        margin: 20px auto; /* Centered with auto margins, added some top margin */
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Image Section - Adopted from your provided HTML/CSS */
    .image-section {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 50px; /* From your example */
        margin-bottom: 30px; /* Added for spacing below */
    }

    .image-box {
        position: relative;
        width: 250px;
        height: 250px;
        border: 5px solid #D9D9D9; /* From your example */
        overflow: hidden;
        border-radius: 10px; /* Rounded corners for consistency */
        box-shadow: var(--shadow); /* Added for consistency */
    }

    .image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease-in-out; /* Added hover effect */
    }

    .image-box:hover img {
        transform: scale(1.05); /* Slight zoom on hover */
    }

    .overlay {
        position: absolute;
        bottom: 10px; /* From your example */
        left: 50%;
        transform: translateX(-50%);
        color: #FFFFFF; /* From your example */
        font-weight: bold; /* From your example */
        font-size: 18px; /* From your example */
        background: rgba(0, 0, 0, 0.6); /* From your example */
        padding: 5px 15px; /* From your example */
        border-radius: 5px; /* Added for consistency */
        white-space: nowrap; /* Prevent text wrapping */
    }

    /* Search Bar Specific Styles (example, you might have your own) */
    .search-bar-container {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }

    .search-input {
        width: 100%;
        max-width: 500px;
        padding: 10px 20px;
        border: 1px solid var(--gray-medium);
        border-radius: 25px;
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        box-shadow: var(--shadow);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(48, 223, 171, 0.3); /* Focus glow */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        

        .image-section {
            flex-direction: column;
            align-items: center;
        }

        .image-box {
            width: 90%;
            height: auto;
        }
    }

    @media (max-width: 480px) {
        

        .search-input {
            font-size: 14px;
            padding: 8px 15px;
        }
    }
  </style>
</head>
<body>
<?php include_once 'component/navs.php'; ?>

    <div class="content">
      <div class="image-section">
        <div class="image-box">
          <a href="search.php?category=musicians"><img src="images/Musicianss.jpg" alt="Musicians"></a>
          <div class="overlay">MUSICIANS</div>
        </div>
          <div class="image-box">
            <a href="search.php?category=bands"><img src="images/Bands.jpg" alt="Bands"></a>
            <div class="overlay">BANDS</div>
          </div>
        </div>

        <div class="search-bar-container">
            <input type="text" class="search-input" placeholder="Search for musicians or bands...">
            </div>

        <div id="search-results">
            <p style="text-align: center; color: var(--text-light);">
                Enter your search query above or select a category.
            </p>
        </div>

      </div>

</body>
</html>