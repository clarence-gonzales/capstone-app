<?php
$host = "127.0.0.1:3307";
$username = "root";
$password = "";
$dbname = "clarence";

$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    // $password = $_POST["password"];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  
    // $stmt = $conn->prepare("INSERT INTO hash (username, password) VALUES (?, ?)");
    // $stmt->bind_param("ss", $plaintext, $hashed);
    // $username = $_POST["username"];
    // $password = $_POST["password"];
    // $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO hash (username, password) VALUES (?, ?)");
    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
  }

    $stmt->bind_param("ss", $username, $hashedPassword);

        $stmt->execute();
        $stmt->close();
    }


    $result = $conn->query("SELECT * FROM hash");
    if (!$result) {
      die("Query failed: " . $conn->error);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hashing Example</title>
</head>
<body>
    <h1>Insert and Display Hashed Data</h1>
    <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <button type="submit">Add</button>
</form>

    <!-- <form method="POST">
        <input type="text" name="text" required>
        <button type="submit">Add</button>
    </form> -->

    <h2>Database Records</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Plaintext</th>
            <th>Hashed</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= htmlspecialchars($row["username"]) ?></td>
            <td><?= $row["password"] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>