<?php
include_once '../../secured/functions.php';

$req = $_GET['data'] ?: '';

if (function_exists($req)) {
    echo $req();
} else {
    echo 'false';
}

?>