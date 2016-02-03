<?php
namespace Tunts\Sample;
/**
 * @group Sample
 */
class CounterTest extends \PHPUnit_Framework_TestCase {
	protected $counter;

	function setUp() {
		$this -> counter = new Counter();
	}

	public function tearDown() {
		unset($this -> counter);
	}

	public function test_increment_counter() {
		$expected = $this -> counter -> get() + 3;
		$this -> counter -> inc();
		$this -> counter -> inc();
		$this -> counter -> inc();
		$this -> assertEquals($expected, $this -> counter -> get());
	}

	public function test_decrement_counter() {
		$expected = $this -> counter -> get() - 3;
		$this -> counter -> dec();
		$this -> counter -> dec();
		$this -> counter -> dec();
		$this -> assertEquals($expected, $this -> counter -> get());
	}

}
?>