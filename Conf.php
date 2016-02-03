<?php

date_default_timezone_set('America/Sao_Paulo');

class Conf
{
    public static $resources = array(
        'sample_db' => Array(
            'type' => 'database',
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'root',
            'pass' => '',
            'database' => 'sample',
            'dbType' => 'MySQL'
        )
    );

    public static $logger = array(
        'logPath' => '.',
        'fileName' => 'log.txt',
        'level' => 7,
        'shout' => 0,
    );
}

?>
