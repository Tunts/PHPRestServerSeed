<?php
/**
 * Sample console runnable code
 * @author Wesley Akio
 */
require("bootstrap.php");

use Tunts\Utils\Logger;
use Tunts\Sample\Counter;

$counter = new Counter();
$log = new Logger('Standalone');
$log->info("Sample Started!");

while (true) {
    $counter->inc();
    $log->info("Step " . $counter->get());
    sleep(1);
}
?>