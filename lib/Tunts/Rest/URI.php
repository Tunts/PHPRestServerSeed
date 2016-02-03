<?php
namespace Tunts\Rest;

class URI {

	private $templateURI;
	private $data;
	private $url = "";

	/**
	 * Tokenizes and replaces '{{}}' encased strings by they counterparts in the data array
	 * @param string The URI
	 * @param array The counterparts
	 */
	public function __construct($templateURI, $data) {
		$this -> templateURI = $templateURI;
		$this -> data = $data;
	}

	public function __toString() {
		if($this->url == ""){
			$this->url = preg_replace_callback('/{{\s?(.*?)\s?}}/', array($this, 'tokenize'), $this -> templateURI) . '?' . http_build_query($this -> data);
		}
		return $this->url;
	}

	protected function tokenize($matches) {
		if (isset($this -> data[$matches[1]])) {
			$response = $this -> data[$matches[1]];
			unset($this -> data[$matches[1]]);
			return $response;
		} else {
			return $matches[0];
		}
	}

}
?>