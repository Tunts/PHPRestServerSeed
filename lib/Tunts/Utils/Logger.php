<?php
namespace Tunts\Utils;

use Conf;

class Logger {
	
	const DEBUG		= 7;
	const INFO		= 6;
	const NOTICE	= 4;
	const WARNING	= 2;
	const ERROR		= 1;
	const NONE		= 0;
	
	protected $loggerName;
	
	protected $logPath;
	protected $fileName;
	protected $logLevel;
	protected $shoutLevel;
	
	protected $levels = array(
		7 => 'debug',
		6 => 'info',
		4 => 'notice',
		2 => 'warning',
		1 => 'error'
	);
	
	public function __construct($namespace){
		
		$names = explode('\\', $namespace);
		$this->loggerName = implode('.', $names);
		
		$this->logPath		= $this->getConfiguration($names, 'logPath');
		$this->fileName		= $this->getConfiguration($names, 'fileName');
		$this->logLevel		= $this->getConfiguration($names, 'level');
		$this->shoutLevel	= $this->getConfiguration($names, 'shout');
		
	}
	
	protected function getConfiguration($names, $configuration){
		while(sizeof($names) > 0){
			if(isset(Conf::$logger[implode('.', $names).'.'.$configuration])){
				return Conf::$logger[implode('.', $names).'.'.$configuration];
			}
			array_pop($names);
		}
		return Conf::$logger[$configuration];
	}
	
	protected function log($data, $level){
		if($this->logLevel >= $level || $this->shoutLevel >= $level){
			$now = Date('Y-m-d H:i:s');
			$log = $now." [".$this->levels[$level]."] ".$this->loggerName.": ".$data."\n";
			if($this->logLevel >= $level){
				file_put_contents($this->fileName, $log, FILE_APPEND);
			}
			if($this->shoutLevel >= $level){
				echo $log;
			}
		}
	}
	
	public function error($data){
		$this->log($data, 1);
	}
	
	public function warning($data){
		$this->log($data, 2);
	}
	
	public function notice($data){
		$this->log($data, 4);
	}
	
	public function info($data){
		$this->log($data, 6);
	}
	
	public function debug($data){
		$this->log($data, 7);
	}
}
?>
