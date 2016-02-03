<?php
namespace Tunts\Sample;
class Counter {
	protected $counter = 0;
	
	public function inc(){
		$this->counter ++;
	}
	
	public function dec(){
		$this->counter --;
	}
	
	public function get(){
		return $this->counter;
	}
}
