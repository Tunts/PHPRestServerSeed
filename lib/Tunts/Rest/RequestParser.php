<?php
namespace Tunts\Rest;

use Tunts\Utils\Logger;

class RequestParser {
	
	public static function getRequest(){
		$request = new Request();
		
		$request->method = self::getMethod();
		$request->urlParams = self::getURLParams();
		$request->queryParams = self::getQueryParams();
		$request->body = self::getBody();
		
		return $request;
	}
	
	public static function getMethod(){
		return $_SERVER['REQUEST_METHOD'];
	}
	
	public static function getURLParams(){
		return explode('/', $_SERVER['PATH_INFO']);
	}
	
	public static function getQueryParams(){
		$queryParams = array();
		parse_str($_SERVER['QUERY_STRING'], $queryParams);
		return $queryParams;
	}
	
	public static function getBody(){
		$body = file_get_contents("php://input");
        if(!isset($_SERVER['CONTENT_TYPE'])) {
            return $body;
        }
		$handler = new Handler();
		return $handler->parse($_SERVER['CONTENT_TYPE'], $body);
	}
}
?>