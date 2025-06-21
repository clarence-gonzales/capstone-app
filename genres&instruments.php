<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <link rel="stylesheet" href="landingpage.css">
  <title>Genre & Instruments</title>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #FFFFFF
    }

    .container {
      text-align: center;
      background: #FFFFFF;
      padding: 30px;
      border-radius: 20px;
    }

    .genre-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      max-width: 400px;
    }

    .instruments-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      max-width: 400px;
    }

    .label {
      text-align: left;
    }

    label {
      padding: 10px 15px;
      border-radius: 20px;
      border: 1px solid #ccc;
      cursor: pointer;
      background-color: lightgray;
      display: inline-block;
    }

    input[type="checkbox"] {
      display: none;
    }

    input[type="checkbox"]:checked + label {
      background-color: #000000;
      color: white;
    }

    .submit-btn {
      width: 100%;
      margin-top: 15px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      background-color: #333;
      color: #FFFFFF;
      border: none;
      border-radius: 25px;
      font-weight: bold;
      transition: 0.3s;
    }

    .submit-btn:hover {
      opacity: 0.7;
    }

    .submit-btn a {
      text-decoration: none;
      color: #FFFFFF;
    }
  </style>

</head>
<body>

  <div class="container">
    <h2 class="heading"><span style="color: gray;">Welcome to </span>BandMate</h2>
    <h3 class="label">Genre</h3>
    
    <form method="post">
      <div class="genre-container">
        <?php
          $genres = ["Rock", "Pop", "Jazz", "Blues", "R&B", "Heavy Metal", "Alternative Rock", "Disco", "Classical Music", "Progressive Rock", "Indie Rock"];

          foreach ($genres as $genre) {
            echo '<input type="checkbox" id="'. $genre .'" name="genres[]" value="'. $genre .'">';
            echo '<label for="'. $genre .'">'. $genre .'</label>';
          }
        ?>
      </div>
      <br>

      <h3 class="label">Instruments</h3>    
      <div class="instruments-container">
        <?php
          $instruments = ["Drums", "Violin", "Keyboard", "Electric Guitar", "Acoustic Guitar", "Bass Guitar", "Saxophone"];

          foreach ($instruments as $instrument) {
            echo '<input type="checkbox" id="'. $instrument .'"     name="instruments[]" value="'. $instrument .'">';
            echo '<label for="'. $instrument .'">'. $instrument .'</label>';
          }
        ?>
      </div>
      <br>
      <button class="submit-btn"><a href="signup.php">Proceed</a</button>

    </form>
  </div>

</body>
</html>