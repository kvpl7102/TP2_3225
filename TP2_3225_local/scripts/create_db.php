<?php
$facts = json_decode(file_get_contents('facts.json'), true);

$servername = "localhost";
$username = "root";
$password = null;

// Create connection
// $conn =  new mysqli($servername, $username, $password);

// // Check connection
// if (!$conn) {
//   die("Connection failed: " . mysqli_connect_error());
// }

// // Create database
// $sql = "CREATE DATABASE IF NOT EXISTS ConceptNetDB";
// if (mysqli_query($conn, $sql)) {
//   echo "Database created successfully";
// } else {
//   echo "Error creating database: " . mysqli_error($conn);
// }

$db = new mysqli($servername, $username, $password);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$db->query("CREATE DATABASE IF NOT EXISTS ConceptNetDB");
$db->select_db("ConceptNetDB");

$db->query("
    CREATE TABLE IF NOT EXISTS Facts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        start VARCHAR(255),
        relation VARCHAR(255),
        end VARCHAR(255)
    )
");

$stmt = $db->prepare("INSERT INTO Facts (start, relation, end) VALUES (?, ?, ?)");

foreach ($facts as $fact) {
    $start = $fact['start'];
    $relation = $fact['rel'];
    $end = $fact['end'];
    $stmt->bind_param("sss", $start, $relation, $end);
    $stmt->execute();
}

$stmt->close();
$db->close();
// mysqli_close($conn);
?>