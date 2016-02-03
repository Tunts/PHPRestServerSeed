<?php
namespace Tunts\Rest;

use Tunts\Utils\Logger;

class Handler {
	protected $handlers;
	
	public function __construct(){
		$this->handlers = array(
			\Httpful\Mime::JSON => new \Httpful\Handlers\JsonHandler(),
			\Httpful\Mime::XML  => new \Httpful\Handlers\XmlHandler(),
			\Httpful\Mime::FORM => new \Httpful\Handlers\FormHandler(),
			\Httpful\Mime::CSV  => new \Httpful\Handlers\CsvHandler()
		);
	}
	
	public function serialize($mimeType, $payload){
		if(!isset($this->handlers[$mimeType]))
			throw new RestException("Could not find a suitable handler to serialize ".$mimeType, RestException::NO_HANDLER_FOUND);
		return $this->handlers[$mimeType]->serialize($payload);
	}
	
	public function parse($mimeType, $payload){
		if(!isset($this->handlers[$mimeType]))
			throw new RestException("Could not find a suitable handler to parse ".$mimeType, RestException::NO_HANDLER_FOUND);
		return $this->handlers[$mimeType]->parse($payload);
	}
}
?>