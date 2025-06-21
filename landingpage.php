<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LandingPage</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <link rel="stylesheet" href="landingpage.css">


<style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
  }

  body {
    background-color: #FFF;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding: 20px;
  }

  .container {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 90%;
    max-width: 600px;
    justify-content: center;
  }

  .container i {
    font-size: 4rem;
    color: #333;
    margin-bottom: 10px;
  }

  h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 3rem;
    font-weight: bold;
    color: #000000;
    margin: 10px 0;
  }

  p {
    font-size: 1.5rem;
    color: #000000;
    margin-top: -15px;
  }

  .btn {
    background-color: #333;
    color: #FFF;
    padding: 12px 25px;
    font-size: 1.2rem;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-weight: bold;
    width: 80%;
    max-width: 300px;
    height: auto;
    margin-top: 75px;
    transition: 0.3s ease-in-out;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    transition: 0.3s;
  }

  .btn:hover {
    opacity: 0.7;
    transform: scale(1.05);
  } 
  
  /* @media (max-width: 768px) {
    .container i{
      font-size: 3rem;
    }
    
    h1 {
      font-size: 2rem;
    }

    p {
      font-size: 1.2rem;
    }

    .btn {
      font-size: 1rem;
      padding: 10px 20px;
    }
  }

  @media (max-width: 480px) {
    .container i {
      font-size: 2.5rem;
    }

    h1 {
      font-size: 1.8rem;
    }

    p {
      font-size: 1rem;
    }

    .btn {
      font-size: 0.9rem;
      width: 90%;
    }
  } */
 

</style>

</head>
<body>

<?php
echo "<div class='container'>";
echo "<i class='fa-solid fa-wave-square'></i>";
echo "<h1>BandMate</h1>";
echo "<p><em>Waves of Sound, Ocean of Peace</em></p>";
echo "<a href='genres&instruments.php' class='btn'>Get Started</a>";
echo "</div>";
?>

</body>
</html>