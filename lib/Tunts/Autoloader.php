<?php

namespace Tunts;

/**
 * Autoloader that implements the PSR-0 spec for interoperability between
 * PHP software.
 */
class Autoloader
{
	/**
	 * @var	array static collection of application paths
	 */
    public static $path = Array();
    public static $resourcePath = null;
	
	/**
	 * The constructor
	 * 
	 * @param string $baseDirectory optional Directory to add
	 */
    public function __construct($baseDirectory = null)
    {
    	foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
    		$this->addLoadPath($path);
		}
		
		if($baseDirectory == null){
			$baseDirectory = explode(DIRECTORY_SEPARATOR, __DIR__);
			array_pop($baseDirectory);
			array_pop($baseDirectory);
			$baseDirectory = implode(DIRECTORY_SEPARATOR, $baseDirectory);
		}
		
		set_include_path(get_include_path().PATH_SEPARATOR.$baseDirectory.DIRECTORY_SEPARATOR."res");
		
		self::$resourcePath = $baseDirectory.DIRECTORY_SEPARATOR."res";
		$this->addLoadPath(self::$resourcePath);
		$this->addLoadPath($baseDirectory.DIRECTORY_SEPARATOR."lib");

    }
	
	/**
	 * When a single path just isn't enough
	 * 
	 * @param string $directory 
	 */
	public function addLoadPath($directory){
		if(!in_array($directory, self::$path)){
			array_unshift(self::$path, $directory);
		}
	}

	/**
	 * Register this class as the autoloader
	 */
    public static function register()
    {
        spl_autoload_register(array(new self, 'loadClass'), true);
    }

	/**
	 * The autoloder method
	 * 
	 * @param string $className the name of the class to be loaded
	 */
    public function loadClass($className)
    {
        $parts = explode('\\', ltrim($className, '\\'));
		if (false !== strpos(end($parts), '_'))
        	array_splice($parts, -1, 1, explode('_', current($parts)));
		
		$file = implode(DIRECTORY_SEPARATOR, $parts) . '.php';
		
		foreach (self::$path as $path) {
    	
			$path = $path . DIRECTORY_SEPARATOR . $file;
			if (file_exists($path))
                return require $path;
        }
    }
}