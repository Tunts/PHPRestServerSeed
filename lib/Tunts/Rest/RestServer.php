<?php
namespace Tunts\Rest;

use Tunts\Utils\Logger;
use Exception;

class RestServer {
	
	//constants to prevent human error
	const HTTP_HEAD		= 'HEAD';
	const HTTP_GET		= 'GET';
	const HTTP_POST		= 'POST';
	const HTTP_PUT		= 'PUT';
	const HTTP_DELETE	= 'DELETE';
	
	//class variables
	protected $prefix;
	protected $route;
	protected $routes;
	protected $encoding;
	protected $log;
	
	/**
	 * fills a array of the specified enconding type (json default) with the url
	 * 
	 * @param string $prefix    the url
	 * @param string $encoding   enconding type
	 */
	public function __construct($encoding='json'){
		
		$this->prefix = str_replace("/index.php", '', $_SERVER['PHP_SELF']);
		$route = explode('?', $_SERVER['REQUEST_URI']);
		$this->route = str_replace($this->prefix, '', $route[0]);
		$this->encoding = $encoding;
		//log creation for debugging
		$this->log = new Logger(__CLASS__);
	}
	
	/**
	 * register the routes used for the methods 
	 * 
	 * @param string $path			url path to be routed
	 * @param string $httpMethod	HTTP method
	 * @param string $class 		class
	 * @param string $method		class method name
	 * @param string $encoding		response encoding type
	 */
	public function addRoute($path, $httpMethod, $class, $method = null, $encoding = null){
		
		//checks if path, method and class are valid
		if(	!isset($path) ||
			!isset($httpMethod) ||
			!isset($class))
			{
			throw new RestException('Mandatory parameter missing on '.$path, RestException::INVALID_ROUTE);
		}
			
		if($method === null)
				$method = strtolower($httpMethod);
		
		if($encoding === null)
			$encoding = $this->encoding;
		
		$tokens = $this->tokenizePath($path);
		$pattern = $this->buildPattern($path, $tokens);
		
		$this->routes[$httpMethod][$pattern] = array('class' => $class, 'method' => $method, 'encoding' => $encoding, 'tokens' => $tokens);
	}
	
	/**
	 * receives a package of routes and send it to the addRoute function
	 * 
	 * @param array $routes  array of routes to be registered
	 */
	public function addRoutes($routes){
		foreach ($routes as $route) {
			if(	!isset($route['path']) ||
				!isset($route['httpMethod']) ||
				!isset($route['class']))
				{
				throw new RestException('Mandatory parameter missing on '.$route['path'], RestException::INVALID_ROUTE);
			}
			
			if(!isset($route['method']))
				$route['method'] = strtolower($route['httpMethod']);
			
			if(!isset($route['encoding']))
				$route['encoding'] = $this->encoding;
			
			$this->addRoute($route['path'], $route['httpMethod'], $route['class'], $route['method'], $route['encoding']);
		}
	}
	
	/**
	 * where the communication is stablished
	 */
	public function route(){

		$this->cors();

		if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") die();

		foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $pattern => $route){
			$match = preg_match($pattern, $this->route, $pathData);


			if($match){
				array_shift($pathData);

				$pathParams = array();

				// combine the names and the values
				foreach ($route['tokens'] as $id => $param){
					$pathParams[$param] = $pathData[$id];
				}

				$params = array_merge($pathParams, $_GET);

				$className = $route['class'];
				$methodName = $route['method'];

				$params['requestBody'] = $this->readBody();

				try {
					$data = $this->invoke($className, $methodName, $params);
				} catch (\Exception $e){
					$this->log->debug($e);
					header("HTTP/1.1 500 Internal server error");
					return '';
				}

				return $this->respond($route['encoding'], $data);
			}
		}

		header("HTTP/1.0 404 Not Found");
		header('Content-type: text/plain');
		echo "The resource $this->route does not exist in this server or does not allow ".$_SERVER['REQUEST_METHOD']. " requests.";
	}
	
	protected function readBody(){
		$content = null;
		
		if($_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "PUT"){
			$handler = new Handler();
				
			$contentType = explode(";",$_SERVER["CONTENT_TYPE"]);
			$data = file_get_contents("php://input");
			$content = "";

			foreach ($contentType as $value) {
				try{
					$content = $handler->parse(trim($contentType[0]), $data);
					break;
				} catch(Exception $e){
					//nothing to do here
				}
			}
		}
		
		return $content;
	}
	
	/**
	 * converts the data to the specified data type
	 * 
	 * @param string $type    enconding type
	 * @param array $data      data to be converted
	 */
	protected function respond($type, $data, $raw = false){
		$this->log->debug("Trying to respond with ".print_r($data, true));
		$contentType = \Httpful\Mime::getFullMime($type);
		
		if($raw){
			header('Content-type: '.$contentType);
			echo $data;
			return;
		}
		
		switch($type){
			case 'json':
				$method = 'toJSON';
				break;
			case 'xml':
				$method = 'toXML';
				break;
		}
		
		header('Content-type: '.$contentType);
		
		if(isset($method) && is_callable(array($data, $method))){
			echo call_user_func(array($data, $method));
		} else {
			try{
				$handler = new Handler();
				echo $handler->serialize($contentType, $data);
			} catch (RestException $re){
				//everithing failed... this must be a string!
				$this->log->info("hello");
				echo $data;
			}
		}
	}
	
	protected function tokenizePath($path){
		preg_match_all("/{[_a-z]\w*}/i", $path, $pathParams);
		return str_replace(array('{','}'), array('',''), $pathParams[0]);
	}
	
	protected function buildPattern($path, $tokens){
		// build a valid regex to parse the path
		$pattern = $path;
		foreach ($tokens as $token){
			$pattern = str_replace('{'.$token.'}', "([^/]*)", $pattern);
		}
		$pattern = "!^".$pattern."$!i";
	
		return $pattern;
	}

    protected function cors(){
        $header = getallheaders();
        if (isset($header['Origin'])) {
            header('Access-Control-Allow-Origin: ' . $header['Origin']);
        }
        if (isset($header['Access-Control-Request-Headers'])) {
            header('Access-Control-Allow-Headers: ' . $header['Access-Control-Request-Headers']);
        }
        if (isset($header['Access-Control-Request-Method'])) {
            header('Access-Control-Allow-Methods: ' . $header['Access-Control-Request-Method']);
        }
    }
	
	public function invoke($className, $method, $data){
	
		$args = array();
	
		$reflectionMethod = new \ReflectionMethod($className, $method);
		$reflectionParameters = $reflectionMethod->getParameters();
	
		foreach ($reflectionParameters as $reflectionParameter ) {
			$name = $reflectionParameter->getName();
				
			$parameterClass = $reflectionParameter->getClass();
			if($parameterClass instanceof \ReflectionClass){
				$parameterClassName = $parameterClass->getName();
			}
				
			if(isset($data[$name])){
				$args[$name] = $data[$name];
			} else {
				$args[$name] = null;
			}
		}
	
		$instance = new $className;
		
		$this->log->info("Calling method ".$method." from class ".$className);
		$this->log->debug("Calling method $className::$method with ".print_r($args, true));
	
		return $reflectionMethod->invokeArgs($instance, $args);
	
	}
}

?>