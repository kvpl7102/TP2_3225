<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'scripts/conn_db.php';

$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

switch ($request[0]) {
    case 'concepts' : // Get all concepts in the database
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            getConcepts($conn);
        }
        break;

    case 'relations' : // Get all relations in the database
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            getRelations($conn);
        }
        break;

    case 'users' : // Get all users or create a new user
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            getUsers($conn);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            createUser($conn);
        }
        break;

    case 'help' : // Display the API documentation
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            getHelp();
        }
        break;
    default:
        http_response_code(404);
}

function getConcepts($conn) { // Get all concepts in the database
    $sql = "SELECT DISTINCT start,end FROM facts";
    $result = mysqli_query($conn, $sql);
    $concepts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($concepts);
}

function getRelations($conn) { // Get all relations in the database
    $sql = "SELECT DISTINCT relation FROM facts";
    $result = mysqli_query($conn, $sql);
    $relations = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($relations);
}

function getUsers($conn) { // Get all users with their score in the database
    $sql = "SELECT username, score FROM users";
    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($users);
}

function createUser($conn) { // Create a new user in the database
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "INSERT INTO users (username, password, score) VALUES ('$username', '$password', 0)";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

function getHelp() { // Display the API documentation
    $documentation = "
    Bienvenue sur l'API! Voici les points de terminaison disponibles:

    GET /concepts: Renvoie une liste de tous les concepts uniques dans la table des faits.
    GET /relations: Renvoie une liste de toutes les relations uniques dans la table des faits.
    GET /users: Renvoie une liste de tous les utilisateurs et leurs scores.
    POST /users: Crée un nouvel utilisateur avec un score de 0. Le nom d'utilisateur et le mot de passe doivent être envoyés dans le corps de la requête.

    Tous les points de terminaison renvoient des données au format JSON.";

    echo $documentation;
}
?>

?>