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
            });

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
                                        <th style='text-align: center'>Start node label</th>
                                        <th style='text-align: center'>Relation</th>
                                        <th style='text-align: center'>End node label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $query_facts_all = "SELECT * FROM facts";
                                        $result = $conn->query($query_facts_all);

                                        $facts = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                        foreach ($facts as $fact) {
                                            echo "<tr>";
                                            echo "<td style='text-align: center'>" . strtolower($fact['start']) . "</td>";
                                            echo "<td style='text-align: center'>" . $fact['relation'] . "</td>";
                                            echo "<td style='text-align: center'>" . strtolower($fact['end']) . "</td>";
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

            this.get('#/concept/:langue/:concept', function(context) {
                var concept = context.params.concept;
                var langue = context.params.langue;

                $('#game-content').html(`
                    <div class="d-flex flex-column align-items-center">
                        <h4 id="concept-header">Concept actuel: ${concept} </h4>
                        <h4 id="langue-header">Langue actuelle: ${langue} </h4>

                        <label for="langue" style="margin-right: 5px;">Langue (en/fr):</label>
                        <input type="text" id="langue" name="langue" style="margin-right: 10px;">
                        <label for="concept" style="margin-right: 5px;">Concept:</label>
                        <input type="text" id="concept" name="concept" style="margin-right: 10px;">
                        <button id="query-button" style="margin-top: 20px;">Query ConceptNet</button><br>
                    </div>
                    
                    <div class="container">
                        <div class="row text-center">
                            <div class="col lg-2"></div>
                            <div class="col lg-12">
                                <div class="row">
                                    <h2>Table de concept requis</h2><br>
                                </div>
                            </div>
                            <div class="col lg-2"></div>
                        </div>
                        <div class="table responsive">
                            <table class="table table-bordered table-hover" id="query-concept-table">
                                <thead>
                                    <tr>
                                        <th style='text-align: center'>Start node label</th>
                                        <th style='text-align: center'>Relation</th>
                                        <th style='text-align: center'>End node label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                `);

                // Make AJAX request to fetch data from ConceptNet API
                $.ajax({
                    url: `https://api.conceptnet.io/query?node=/c/${langue}/${concept}`,
                    type: 'GET',
                    data: {
                        concept: concept,
                        langue: langue
                    },
                    success: function(data) {
                        var edges = data.edges; 
                        var tableBody = $('#query-concept-table tbody');

                        edges.forEach(function(edge) {
                            var startLabel = edge.start.label.toLowerCase();
                            var relation = edge.rel.label;
                            var endLabel = edge.end.label.toLowerCase();

                            var tableRow = `
                                <tr>
                                    <td style='text-align: center'>${startLabel}</td>
                                    <td style='text-align: center'>${relation}</td>
                                    <td style='text-align: center'>${endLabel}</td>
                                </tr>
                            `;

                            tableBody.append(tableRow);
                        });
                        
                        $('#query-concept-table').DataTable(
                            {
                                "info": true,
                                "paging": true,
                                "ordering": false,
                                "searching": true, 
                                "scrollY": "250px",
                                "scrollCollapse": true,                                                         
                            }
                        );
                    }
                });

                // If query button is clicked, validate the input and redirect to the new URL of corresponding concept
                $('#query-button').click(function() {
                    var langue = $('#langue').val();
                    var concept = $('#concept').val();

                    
                    if (langue.toLowerCase() !== 'en' && langue.toLowerCase() !== 'fr') {
                        alert('Langue invalide. Veuillez entrer "en" ou "fr".');
                        $('#langue').focus();
                        return;
                    }

                    if (!concept) {
                        alert('Veuillez entrer un concept.');
                        $('#concept').focus();
                        return;
                    }

                    // Update the headers
                    $('#concept-header').text('Concept actuel: ' + concept);
                    $('#langue-header').text('Langue actuelle: ' + langue);

                    // Construct the URL for the ConceptNet API
                    var url = 'https://api.conceptnet.io/query?node=/c/' + langue + '/' + concept;

                    // Redirect to the new URL
                    context.redirect('#/concept/' + langue.toLowerCase() + '/' + concept.toLowerCase());
                        

                    // Make new AJAX request
                    // $.ajax({
                    //     url: url,
                    //     type: 'GET',
                    //     success: function(data) {
                    //         console.log("successfully fetched new data");
                            
                    //         var edges = data.edges;
                    //         var tableBody = $('#query-concept-table tbody');
                            

                    //         // Empty the table
                    //         tableBody.empty();

                    //         // Fill the table with new data
                    //         edges.forEach(function(edge) {
                    //             var startLabel = edge.start.label.toLowerCase();
                    //             var relation = edge.rel.label;
                    //             var endLabel = edge.end.label.toLowerCase();

                    //             var tableRow = `
                    //                 <tr>
                    //                     <td style='text-align: center'>${startLabel}</td>
                    //                     <td style='text-align: center'>${relation}</td>
                    //                     <td style='text-align: center'>${endLabel}</td>
                    //                 </tr>
                    //             `;

                    //             tableBody.append(tableRow);
                    //         });
                    //     },
                    //     error: function() {
                    //         console.log('Error fetching data from ConceptNet');
                    //     }
                    // });
                });
                
            });

            this.get('#/relation/:relation/from/:langue/:concept', function(context) {
                var relation = context.params.relation;
                var langue = context.params.langue;
                var start_concept = context.params.concept;

                $('#game-content').html(`
                    <div class="d-flex flex-column align-items-center">
                        <h5 id="start-concept-header">Concept actuel: ${start_concept} </h5>
                        <h5 id="langue-header">Langue actuelle: ${langue} </h5>
                        <h5 id="relation-header">Relation actuelle: ${relation} </h5>

                        <label for="start-langue" style="margin-right: 5px;">Langue (en/fr):</label>
                        <input type="text" id="start-langue" name="langue" style="margin-right: 10px;">
                        <label for="start-concept" style="margin-right: 5px;">Concept:</label>
                        <input type="text" id="start-concept" name="start-concept" style="margin-right: 10px;">
                        <label for="relation" style="margin-right: 5px;">Relation:</label>
                        <input type="text" id="relation" name="relation" style="margin-right: 10px;">

                        <button id="query-from-button" style="margin-top: 20px;">Query ConceptNet</button><br>
                        <a href="https://github.com/commonsense/conceptnet5/wiki/Relations" target="_blank">Consulter toutes relations de ConceptNet</a><br>


                    </div>
                    <br>
                    <div class="container">
                        <div class="row text-center">
                            <div class="col lg-2"></div>
                            <div class="col lg-12">
                                <div class="row">
                                    <h2>Table des end-noeuds d'un concept requis</h2><br>
                                </div>
                            </div>
                            <div class="col lg-2"></div>
                        </div>
                        <div class="table responsive">
                            <table class="table table-bordered table-hover" id="query-from-concept-table">
                                <thead>
                                    <tr>
                                        <th style='text-align: center'>Concept queried</th>
                                        <th style='text-align: center'>Relation</th>
                                        <th style='text-align: center'>End node label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                `);
                
                $.ajax({
                    url: `https://api.conceptnet.io/query?start=/c/${langue}/${start_concept}&rel=/r/${relation}`,
                    type: 'GET',
                    data: {
                        start_concept: start_concept,
                        langue: langue,
                        relation: relation
                    },
                    success: function(data) {
                        var edges = data.edges; 
                        var tableBody = $('#query-from-concept-table tbody');

                        edges.forEach(function(edge) {
                            var startLabel = edge.start.label.toLowerCase();
                            var rel = edge.rel.label;
                            var endLabel = edge.end.label.toLowerCase();

                            var tableRow = `
                                <tr>
                                    <td style='text-align: center'>${startLabel}</td>
                                    <td style='text-align: center'>${rel}</td>
                                    <td style='text-align: center'>${endLabel}</td>
                                </tr>
                            `;

                            tableBody.append(tableRow);
                        });
                
                        $('#query-from-concept-table').DataTable(
                            {
                                "info": true,
                                "paging": true,
                                "ordering": false,
                                "searching": true, 
                                "scrollY": "250px",
                                "scrollCollapse": true,                                                         
                            }
                        );
                    }
                });

                $('#query-from-button').click(function() {
                    var langue = $('#start-langue').val();
                    var start_concept = $('#start-concept').val();
                    var relation = $('#relation').val();

                    
                    if (langue.toLowerCase() !== 'en' && langue.toLowerCase() !== 'fr') {
                        alert('Langue invalide. Veuillez entrer "en" ou "fr".');
                        $('#start-langue').focus();
                        return;
                    }

                    if (!start_concept) {
                        alert('Veuillez entrer un concept.');
                        $('#start-concept').focus();
                        return;
                    }
                    
                    if (!relation) {
                        alert('Veuillez entrer une relation.');
                        $('#relation').focus();
                        return;
                    }

                    // Update the headers
                    $('#start-concept-header').text('Concept actuel: ' + start_concept);
                    $('#langue-header').text('Langue actuelle: ' + langue);
                    $('#relation-header').text('Relation actuelle: ' + relation);

                    // Construct the URL for the ConceptNet API
                    var url = 'https://api.conceptnet.io/query?start=/c/' + langue.toLowerCase() + '/' + concept.toLowerCase() + '&rel=/r/' + relation;

                    // Redirect to the new URL
                    context.redirect('#/relation/' + relation + '/'+ langue + '/' + start_concept);
                });
                
            });

            this.get('#/relation/:relation', function(context) {
                var relation = context.params.relation;

                $('#game-content').html(`
                    <div class="d-flex flex-column align-items-center">
                        <h4 id="relation-header">Relation actuelle: ${relation} </h4>

                        <label for="relation" style="margin-right: 5px;">Relation:</label>
                        <input type="text" id="relation" name="relation" style="margin-right: 10px;">

                        <button id="query-relation-button" style="margin-top: 20px;">Query ConceptNet</button><br>
                        <a href="https://github.com/commonsense/conceptnet5/wiki/Relations" target="_blank">Consulter toutes relations de ConceptNet</a><br>

                    </div>
                    <br>
                    <div class="container">
                        <div class="row text-center">
                            <div class="col lg-2"></div>
                            <div class="col lg-12">
                                <div class="row">
                                    <h2>Table de relation requise</h2><br>
                                </div>
                            </div>
                            <div class="col lg-2"></div>
                        </div>
                        <div class="table responsive">
                            <table class="table table-bordered table-hover" id="query-relation-table">
                                <thead>
                                    <tr>
                                        <th style='text-align: center'>Start node label</th>
                                        <th style='text-align: center'>Relation</th>
                                        <th style='text-align: center'>End node label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                `);

                $.ajax({
                    url: `https://api.conceptnet.io/query?rel=/r/${relation}&limit=1000`,
                    type: 'GET',
                    data: {
                        relation: relation
                    },
                    success: function(data) {
                        var edges = data.edges; 
                        var tableBody = $('#query-relation-table tbody');

                        edges.forEach(function(edge) {
                            var startLabel = edge.start.label.toLowerCase();
                            var rel = edge.rel.label;
                            var endLabel = edge.end.label.toLowerCase();

                            var tableRow = `
                                <tr>
                                    <td style='text-align: center'>${startLabel}</td>
                                    <td style='text-align: center'>${rel}</td>
                                    <td style='text-align: center'>${endLabel}</td>
                                </tr>
                            `;

                            tableBody.append(tableRow);
                        });
                
                        $('#query-relation-table').DataTable(
                            {
                                "info": true,
                                "paging": true,
                                "ordering": false,
                                "searching": true, 
                                "scrollY": "250px",
                                "scrollCollapse": true,                                                         
                            }
                        );
                    }
                });

                $('#query-relation-button').click(function() {
                    var relation = $('#relation').val();
                    
                    if (!relation) {
                        alert('Veuillez entrer une relation.');
                        $('#relation').focus();
                        return;
                    }

                    // Update the headers
                    $('#relation-header').text('Relation actuelle: ' + relation);

                    // Construct the URL for the ConceptNet API
                    var url = 'https://api.conceptnet.io/query?&rel=/r/' + relation;

                    // Redirect to the new URL
                    context.redirect('#/relation/' + relation);
                });
            });
    
        });
        app.run();
    });
    </script>
    

</html>