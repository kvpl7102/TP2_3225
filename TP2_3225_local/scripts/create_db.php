<?php

// Load facts from JSON file
$facts = json_decode(file_get_contents('facts.json'), true);

// MySQL server connection details
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("DROP DATABASE IF EXISTS ConceptNetDB");
$conn->query("CREATE DATABASE IF NOT EXISTS ConceptNetDB");

// Use the new database
$conn->select_db("ConceptNetDB");

// Create table for facts
$conn->query("DROP TABLE IF EXISTS Facts");
$conn->query("
    CREATE TABLE IF NOT EXISTS Facts (
        idFact VARCHAR(255),
        start VARCHAR(255),
        relation VARCHAR(255),
        end VARCHAR(255)
    )
");

// Insert into the table facts
$stmt = $conn->prepare("INSERT INTO Facts (idFact, start, relation, end) VALUES (?, ?, ?, ?)");
foreach ($facts as $fact) {
    $stmt->bind_param("ssss", $fact['idFact'], $fact['start'], $fact['relation'], $fact['end']);
    $stmt->execute();
}

// Create table for users
$conn->query("DROP TABLE IF EXISTS Users");
$conn->query("
    CREATE TABLE IF NOT EXISTS Users (
        idUser INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )
");

// Insert into the table users
$stmt = $conn->prepare("INSERT INTO Users (username, password) VALUES (?, ?)");
$users = array(
    array('ift3225', '5223tfi'),
    array('user', 'userpassword'),
    array('guest', 'guestpassword'),
    array('test1', 'test1password'),
    array('test2', 'test2password')
);
foreach ($users as $user) {
    $stmt->bind_param("ss", $user[0], $user[1]);
    $stmt->execute();
}

// Close connection
$conn->close();

?>