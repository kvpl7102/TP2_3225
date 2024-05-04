<?php
include('scripts/conn_db.php');
function fetch_conceptnet_data($concept, $lang) {
    $url = "http://api.conceptnet.io/query?node=/c/{$lang}/{$concept}&other=/c/{$lang}&limit=10";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data;
}

$concepts = array(
    "Apple" => "en",
    "Dog" => "en",
    "Rain" => "en",
    "Book" => "en",
    "Tree" => "en",
    "Car" => "en",
    "Sun" => "en",
    "Moon" => "en",
    "Star" => "en",
    "Ocean" => "en",
    "Mountain" => "en",
    "River" => "en",
    "Flower" => "en",
    "Bird" => "en",
    "Cat" => "en",
    "Fish" => "en",
    "Butterfly" => "en",
    "Leaf" => "en",
    "Snow" => "en",
    "Rainbow" => "en",
    "Potato" => "en",
    "Wolf" => "en",
    "Wind" => "en",
    "Chair" => "en",
    "House" => "en",
    "Boat" => "en",
    "Earth" => "en",
    "Cheese" => "en",
    "Light" => "en",
    "Forest" => "en",
    "Beach" => "en",
    "Lake" => "en",
    "Hat" => "en",
    "Lion" => "en",
    "Mouse" => "en",
    "Bird" => "en",
    "Bee" => "en",
    "Paper" => "en",
    "Fire" => "en",
    "Cloud" => "en"
);

$relations = array("IsA", "PartOf", "HasA", "UsedFor", "CapableOf", "AtLocation", "Causes", "HasProperty", "FormOf", "RelatedTo");

$facts = array();

foreach ($concepts as $concept => $lang) {
    $data = fetch_conceptnet_data(strtolower($concept), $lang);
    foreach ($data['edges'] as $edge) {
        $idFact = $edge['@id'];
        $start = $edge['start']['label'];
        $end = $edge['end']['label'];
        $rel = $edge['rel']['label'];

        if (in_array($rel, $relations)) {
            $fact = array(
                "idFact" => $idFact,
                "start" => $start,
                "relation" => $rel,
                "end" => $end
            );
            array_push($facts, $fact);
        }
    }
}

$file = fopen('facts.json', 'w');
fwrite($file, json_encode($facts, JSON_PRETTY_PRINT));
fclose($file);

?>