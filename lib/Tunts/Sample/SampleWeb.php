<?php
namespace Tunts\Sample;
class SampleWeb {
	public function hello(){
		return "hello";
	}
	
	public function obj(){
		 $obj = new \stdClass;
		 $obj -> batata = "banana";
		 $obj -> maca = "abacate";
		 return $obj;
	}
	
	public function arr(){
		return Array(
			"batata"=>"banana",
			"maca"=>"abacate"
		);
	}
	
	public function parameters($params){
		return $params;
	}
}
