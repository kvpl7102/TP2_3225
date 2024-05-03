<?php 
    $servername = "localhost";
    $username = "root";
    $password = ""; // Utilise une chaîne vide pour les configurations sans mot de passe
    $dbName = "ConceptNetDB";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbName);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sammy@0.7.6/lib/sammy.js"></script>
    
</head>
<body>
    <div id="main" class="container mt-5">
        <h1 class="mb-4 text-center">Jeu ConceptNet</h1>
        <div id="game-content" class="mb-3"></div>
        <div id="result" class="alert alert-info"></div>
        <div id="score" class="alert alert-success"></div>
    </div>
    <!-- <div id="main">
        <h1>Jeu ConceptNet</h1>
        <div id="game-content"></div>
        <div id="result"></div>
        <div id="score"></div>
    </div> -->

    <script>
    $(document).ready(function() {
        var app = Sammy('#main', function() {
            this.get('#/help', function(){
                $('#game-content').html(`
                <h2>Aide et Informations sur les Routes</h2>
                <ul>
                    <li><b>#/help</b> : Affiche cette page d'aide avec des informations sur toutes les routes disponibles.</li>
                    <li><b>#/login</b> : Permet à un utilisateur autorisé de s'identifier en utilisant un nom d'utilisateur et un mot de passe.</li>
                    <li><b>#/logout</b> : Permet à un utilisateur de se déconnecter de l'application et de terminer sa session active.</li>
                    <li><b>#/stats</b> : Affiche des statistiques de la base de données, y compris le nombre de concepts, de relations, de faits, et d'utilisateurs.</li>
                    <li><b>#/dump/faits</b> : Affiche une table contenant les faits stockés dans la base de données avec un système de pagination.</li>
                    <li><b>#/jeux/quisuisje/:temps/:indice</b> : Jeu 'Qui suis-je?' où l'utilisateur doit deviner un concept à partir d'indices fournis.</li>
                    <li><b>#/jeux/related/:temps</b> : Jeu 'Related' où l'utilisateur doit entrer des mots liés à un concept donné.</li>
                </ul>
            `);
            })
            this.get('#/jeux/quisuisje/:temps/:indice', function() {
                var totalTime = this.params.temps || 60; 
                var intervalTime = this.params.indice || 10;
                var correctAnswer = "chat"; //Start or End Concept(depends)
                var hints = ["CapableOf miauler", "AtLocation dans la maison", "ReceivesAction caressé"];//(Start+Realation)or(Relation + end)
                var currentIndex = 0;
                var score = Math.ceil(totalTime / intervalTime);
                var interval;

                $('#game-content').html('<h2>Qui suis-je ?</h2>' +
                    '<div id="question"></div>' +
                    '<input type="text" id="answer" placeholder="Votre réponse ici">' +
                    '<button id="submit-button" class="btn btn-primary rounded-pill">Soumettre</button>');

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
            var concept = "chat"; // Simulé, remplacer par un concept aléatoire de la base
            var timeoutId;

            $('#game-content').html(
                '<h2>Related Words to "' + concept + '"</h2>' +
                '<input type="text" id="related-words" class="form-control mb-3" placeholder="Entrez des mots, séparés par des virgules">' +
                '<button id="submit-words" class="btn btn-primary">Soumettre</button>'
            );

            timeoutId = setTimeout(() => {
                evaluateAndDisplayResults();
            }, totalTime * 1000);

            $('#submit-words').click(function() {
                clearTimeout(timeoutId);
                evaluateAndDisplayResults();
            });

            function evaluateAndDisplayResults() {
                var userInput = $('#related-words').val();
                var words = userInput.split(',');
                var validWords = []; // Simulation, remplacer par les vrais mots valides tirés de ConceptNet
                var invalidWords = [];

                words.forEach(word => {
                    word = word.trim().toLowerCase();
                    // Ici, on vérifiera si le mot est lié au concept (simulation)
                    if (word === concept) { // Simplifié pour l'exemple
                        validWords.push(word);
                    } else {
                        invalidWords.push(word);
                    }
                });

                $('#result').html(
                    '<p>Mots valides: ' + validWords.join(', ') + '</p>' +
                    '<p>Mots invalides: ' + invalidWords.join(', ') + '</p>'
                );
                $('#score').text('Score: ' + validWords.length);
            }
            });
        });

        app.run();
    });
    </script>
</body>
</html>
