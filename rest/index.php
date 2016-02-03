<?php
/**
 * Sample
 */
require_once ("bootstrap.php");

use Tunts\Rest\RestServer;

$server = new RestServer('/directcall-server/rest');
$server -> addRoute('/hello', 'GET', 'Tunts\SampleSampleWeb', 'hello', 'json');
$server -> addRoute('/obj', 'GET', 'Tunts\SampleSampleWeb', 'obj', 'json');
$server -> addRoute('/arr', 'GET', 'Tunts\SampleSampleWeb', 'arr', 'json');
$server -> addRoute('/parameters/{param}', 'GET', 'Tunts\SampleSampleWeb', 'parameters', 'json');
$server -> route();
?>