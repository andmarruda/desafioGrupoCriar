<?php
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    require_once __DIR__ . '/../src/autoload.php';

    use \challenge\race;

    $r = new race(__DIR__.'/../sampledata.log');
?>