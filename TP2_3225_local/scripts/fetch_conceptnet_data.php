<?php
function fetch_conceptnet_data($start, $rel, $end, $lang='en') {
    $url = "http://api.conceptnet.io/query?node=/c/{$lang}/{$start}&rel=/r/{$rel}&other=/c/{$lang}/{$end}";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

$concepts = [
    'doctor', 'médecin',
    'nurse', 'infirmière',
    'teacher', 'enseignant',
    'engineer', 'ingénieur',
    'scientist', 'scientifique',
    'lawyer', 'avocat',
    'journalist', 'journaliste',
    'chef', 'chef',
    'actor', 'acteur',
    'musician', 'musicien',
    'writer', 'écrivain',
    'artist', 'artiste',
    'photographer', 'photographe',
    'designer', 'designer',
    'architect', 'architecte',
    'dentist', 'dentiste',
    'pharmacist', 'pharmacien',
    'psychologist', 'psychologue',
    'veterinarian', 'vétérinaire',
    'pilot', 'pilote'
];
$relationships = [
    'IsA', // e.g., A doctor IsA professional
    'PartOf', // e.g., A pilot is PartOf an airline crew
    'HasA', // e.g., A chef HasA kitchen
    'UsedFor', // e.g., A scientist is UsedFor research
    'CapableOf', // e.g., A teacher is CapableOf educating students
    'AtLocation', // e.g., A pharmacist is AtLocation pharmacy
    'Requires', // e.g., A journalist Requires a source
    'Desires', // e.g., An artist Desires creativity
    'CreatedBy', // e.g., A book is CreatedBy a writer
    'HasPrerequisite' // e.g., A veterinarian HasPrerequisite a degree in veterinary medicine
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