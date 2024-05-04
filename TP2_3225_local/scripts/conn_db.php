<?php 
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbName = "ConceptNetDB";

    try {
        $conn = mysqli_connect($servername, $username, $password, $dbName);
        
        
    } catch (mysqli_sql_exception $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    


?>