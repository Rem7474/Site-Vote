<?php
// Point d'entrée racine: redirige vers le site public en conservant la query string
$qs = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? ('?' . $_SERVER['QUERY_STRING']) : '';
header('Location: public/index.php' . $qs);
exit();
?>
