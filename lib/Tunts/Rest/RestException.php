<?php
namespace Tunts\Rest;

use \Exception;

class RestException extends Exception{
	const ROUTE_NOT_FOUND		= 1;
	const INVALID_ROUTE			= 2;
	const NO_HANDLER_FOUND		= 3;
}