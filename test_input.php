<?php
header('Content-Type: text/plain');
$raw = file_get_contents('php://input');
echo 'RAW=';
echo $raw;
echo PHP_EOL;
$decoded = json_decode($raw, true);
echo 'JSON=';
var_export($decoded);
echo PHP_EOL;
echo 'POST=';
var_export($_POST);
echo PHP_EOL;
echo 'GET=';
var_export($_GET);
