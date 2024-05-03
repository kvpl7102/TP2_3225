<?php
function fetch_conceptnet_data($start, $rel, $end, $lang='en') {
    $url = "http://api.conceptnet.io/query?node=/c/{$lang}/{$start}&rel=/r/{$rel}&other=/c/{$lang}/{$end}";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

$concepts = [
    
];
$relationships = [

];

$facts = [];

for ($i = 0; $i < 100; $i++) {
    $start = $concepts[$i % count($concepts)];
    $rel = $relationships[$i % count($relationships)];
    $end = $concepts[($i+1) % count($concepts)];
    $data = fetch_conceptnet_data($start, $rel, $end);
    $facts[] = $data;
}

file_put_contents('facts.json', json_encode($facts));
?>