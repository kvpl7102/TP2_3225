<?php 
    include('scripts/conn_db.php');

    // Login validation for the user using Db
    if (isset($_POST['submit'])) { // If the submit button is clicked
        $username = $_POST['username'];
        $password = $_POST['password'];
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'" ;
        $result = $conn->query($sql);

        if (!$result) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $userInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (count($userInfo) > 0) {
            
            $_SESSION['user'] = $username;            
            header("Location: #/menu");
            
        } else {
            echo "invalid credentials";
            // header("Location: #/login");
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Jeu ConceptNet</title>

    <!-- CSS links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.6/css/dataTables.dataTables.css" />
  
    <!-- JS links -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.6/js/dataTables.js"></script>
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
            this.get('#/help', function() {
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
            this.get('#/login', function() {
                $('#game-content').html(`
                <h2>Connexion</h2>
                <form id="login-form" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Se connecter</button>
                </form>
            `);
            });

            this.get('#/menu', function() {

                $('#game-content').html(`
                    <h2>Menu Principal</h2>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="#/jeux/quisuisje/60/10">Jouer à 'Qui suis-je?'</a></li>
                        <li class="nav-item"><a class="nav-link" href="#/jeux/related/60">Jouer à 'Related'</a></li>
                        <li class="nav-item"><a class="nav-link" href="#/stats">Afficher les Statistiques</a></li>
                        <li class="nav-item"><a class="nav-link" href="#/dump/faits">Voir les Faits</a></li>
                        <li class="nav-item"><a class="nav-link" href="#/logout">Se Déconnecter</a></li>
                    </ul>
                `);
            });

            this.get('#/logout', function() {
                sessionStorage.removeItem('user');
                alert('Vous avez été déconnecté.');
                location.hash = '#/help';
            });

            this.get('#/jeux/quisuisje/:temps/:indice', function() {
                var totalTime = this.params.temps || 60;
                var intervalTime = this.params.indice || 10;
                var correctAnswer = "chat"; //Start or End Concept(depends)
                var hints = ["CapableOf miauler", "AtLocation dans la maison",
                    "ReceivesAction caressé"
                ]; //(Start+Realation)or(Relation + end)
                var currentIndex = 0;
                var score = Math.ceil(totalTime / intervalTime);
                var interval;

                $('#game-content').html('<h2>Qui suis-je ?</h2>' +
                    '<div id="question"></div>' +
                    '<input type="text" id="answer" placeholder="Votre réponse ici">' +
                    '<button id="submit-button" class="btn btn-primary rounded-pill">Soumettre</button>'
                );

                function showHint() {
                    if (currentIndex < hints.length && $('#result').text() === "") {
                        $('#question').text(hints[currentIndex]);
                        currentIndex++;
                        score--;
                    } else {
                        clearInterval(interval);
                        $('#question').text("Temps écoulé! La réponse était : " +
                            correctAnswer);
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
                    var
                        validWords = []; // Simulation, remplacer par les vrais mots valides tirés de ConceptNet
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

            this.get('#/stats', function() {
                $('#game-content').html(`
                    <div class="container">
                        <div class="row text-center">
                            <div class="col lg-2"></div>
                            <div class="col lg-8">
                                <div class="row">
                                    <h2>Statistiques de la base</h2><br><br>
                                    <br>
                                </div>
                                <div class="row">
                                    <?php 
                                        // Count the number of users
                                        $query_stats_numusers = "SELECT COUNT(DISTINCT idUser) FROM users"; 
                                        $result_numusers = $conn->query($query_stats_numusers);
                                        if($result_numusers) {
                                            $numusers = mysqli_fetch_all($result_numusers, MYSQLI_ASSOC);
                                            echo "<h4>Nombre d'utilisateurs: " . $numusers[0]['COUNT(DISTINCT idUser)'] . "</h4>";
                                        } else {
                                            echo "<h4>�chec de requ�te � la base :(</h4>";
                                        }
                                        
                                        // --------------------------------------------------------------
                                        // Count the number of facts
                                        $query_stats_facts = "SELECT COUNT(DISTINCT idFact) FROM facts"; 
                                        $result_facts = $conn->query($query_stats_facts);
                                        if ($result_facts) {
                                            $facts = mysqli_fetch_all($result_facts, MYSQLI_ASSOC);
                                            echo "<h4>Nombre de faits: " . $facts[0]['COUNT(DISTINCT idFact)'] . "</h4>";
                                        } else {
                                            echo "<h4>�chec de requ�te � la base :(</h4>";
                                        }

                                        // --------------------------------------------------------------
                                        // Count the number of concepts
                                        $query_stats_concepts = "SELECT COUNT(DISTINCT start, end) FROM facts"; 
                                        $result_concepts = $conn->query($query_stats_concepts);
                                        if ($result_concepts) {
                                            $concepts = mysqli_fetch_all($result_concepts, MYSQLI_ASSOC);
                                            echo "<h4>Nombre de concepts: " . $concepts[0]['COUNT(DISTINCT start, end)'] . "</h4>";
                                        } else {
                                            echo "<h4>�chec de requ�te � la base :(</h4>";
                                        }

                                        // --------------------------------------------------------------
                                        // Count the number of relations
                                        $query_stats_relations = "SELECT COUNT(DISTINCT relation) FROM facts"; 
                                        $result_relations = $conn->query($query_stats_relations);
                                        if ($result_relations) {
                                            $relations = mysqli_fetch_all($result_relations, MYSQLI_ASSOC);
                                            echo "<h4>Nombre de relations: " . $relations[0]['COUNT(DISTINCT relation)'] . "</h4>";
                                        } else {
                                            echo "<h4>�chec de requ�te � la base :(</h4>";
                                        }
                                    ?>
                                </div>  
                            </div>
                            <div class="col lg-2"></div>
                        </div>
                    </div>
                `  
                );
            });

            this.get('#/dump/faits' ,function(){
                $('#game-content').html(`
                    <div class="container">
                        <div class="row text-center">
                            <div class="col lg-2"></div>
                            <div class="col lg-12">
                                <div class="row">
                                    <h2>Table des Faits</h2><br>
                                </div>
                            </div>
                            <div class="col lg-2"></div>
                        </div>
                        <div class="table responsive">
                            <table class="table table-bordered table-hover" id="facts-table">
                                <thead>
                                    <tr>
                                        <th>Start</th>
                                        <th>Relation</th>
                                        <th>End</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $query_facts_all = "SELECT * FROM facts";
                                        $result = $conn->query($query_facts_all);

                                        $facts = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                        foreach ($facts as $fact) {
                                            echo "<tr>";
                                            echo "<td>" . strtolower($fact['start']) . "</td>";
                                            echo "<td>" . $fact['relation'] . "</td>";
                                            echo "<td>" . strtolower($fact['end']) . "</td>";
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                `);

                $.ajax({
                    url: 'mon_jeu.php',
                    type: 'GET',
                    success: function(data) {
                        $('#facts-table').DataTable(
                            {
                                "info": true,
                                "paging": true,
                                "ordering": false,
                                "searching": true, 
                                "scrollY": "300px",
                                "scrollCollapse": true,                                                         
                            }
                         );
                    }
                });
            });
        });

        app.run();
    });
    </script>


</html>