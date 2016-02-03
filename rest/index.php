<?php
/**
 * Sample
 */
require_once ("bootstrap.php");

use Tunts\Rest\RestServer;

$server = new RestServer('/directcall-server/rest');
$server -> addRoute('/hello', 'GET', 'Tunts\Sample\SampleWeb', 'hello', 'json');
$server -> addRoute('/obj', 'GET', 'Tunts\Sample\SampleWeb', 'obj', 'json');
$server -> addRoute('/arr', 'GET', 'Tunts\Sample\SampleWeb', 'arr', 'json');
$server -> addRoute('/parameters/{param}', 'GET', 'Tunts\Sample\SampleWeb', 'parameters', 'json');
$server -> route();
?>