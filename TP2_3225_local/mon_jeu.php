<?php 
    $servername = "localhost";
    $username = "root";
    $password = null;
    $dbName = "ConceptNetDB";

    // Create connection
    $conn = sqli.connect($servername, $username, $password, $dbName);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Queries
    $sql = "SELECT * FROM $dbName"; // Select all rows from the table

    $sql_stats_facts = "SELECT COUNT(DISTINCT idFact) from $dbName"; // Count the number of distinct facts

    $sql_stats_concepts = "SELECT COUNT(DISTINCT start AS idStart, end AS idEnd) from $dbName"; // Count the number of distinct concepts

    $sql_stats_relations = "SELECT COUNT(DISTINCT relation) from $dbName"; // Count the number of distinct relations

    $sql_stats_user = "SELECT COUNT(DISTINCT user) from user"; // Count the number of distinct users

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jeu ConceptNet</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sammy@0.7.6/lib/sammy.js"></script>
</head>
<body>
    <div id="main">
        <h1>Jeu ConceptNet</h1>
        <div id="game-content"></div>
        <div id="result"></div>
        <div id="score"></div>
    </div>

    <script>
    $(document).ready(function() {
        var app = Sammy('#main', function() {
            this.get('#/jeux/quisuisje/:temps/:indice', function() {
                var totalTime = this.params.temps || 60; 
                var intervalTime = this.params.indice || 10;
                var correctAnswer = "chat"; 
                var hints = ["CapableOf miauler", "AtLocation dans la maison", "ReceivesAction caressé"];
                var currentIndex = 0;
                var score = Math.ceil(totalTime / intervalTime);
                var interval;

                $('#game-content').html('<h2>Qui suis-je ?</h2>' +
                    '<div id="question"></div>' +
                    '<input type="text" id="answer" placeholder="Votre réponse ici">' +
                    '<button id="submit-button">Soumettre</button>');

                function showHint() {
                    if (currentIndex < hints.length && $('#result').text() === "") {
                        $('#question').text(hints[currentIndex]);
                        currentIndex++;
                        score--;
                    } else {
                        clearInterval(interval);
                        $('#question').text("Temps écoulé! La réponse était : " + correctAnswer);
                        $('#score').text("Score final : " + score);
                    }
                }

                interval = setInterval(showHint, intervalTime * 1000);
                showHint();

                $('#submit-button').click(function() {
                    var userAnswer = $('#answer').val().toLowerCase();
                    if (userAnswer === correctAnswer) {
                        $('#result').text("Correct! Bien joué.");
                        $('#score').text("Score final : " + score);
                        clearInterval(interval);
                    } else {
                        $('#result').text("Incorrect. Essayez encore!");
                    }
                });
            });

            this.get('#/jeux/related/:temps?', function() {
                var totalTime = this.params.temps || 60;
                var concept = "chat"; 
                $('#game-content').html('<h2>Related Words to "' + concept + '"</h2>' +
                    '<input type="text" id="related-words" placeholder="Entrez des mots, séparés par des virgules">' +
                    '<button id="submit-words">Soumettre</button>');

                setTimeout(() => {
                    var userInput = $('#related-words').val();
                    var words = userInput.split(',');
                    var validWords = [];
                    var invalidWords = [];

                    words.forEach(word => {
                        word = word.trim().toLowerCase();
                        if (word === concept) {
                            validWords.push(word);
                        } else {
                            invalidWords.push(word);
                        }
                    });

                    $('#result').html('<p>Mots valides: ' + validWords.join(', ') + '</p>' +
                        '<p>Mots invalides: ' + invalidWords.join(', ') + '</p>');
                    $('#score').text('Score: ' + validWords.length);
                }, totalTime * 1000);

                $('#submit-words').click(function() {
                    clearTimeout(totalTime);
                });
            });
        });

        app.run();
    });
    </script>
</body>
</html>
