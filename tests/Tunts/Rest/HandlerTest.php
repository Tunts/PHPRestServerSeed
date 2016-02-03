<?php
namespace Tunts\Rest;
/**
 * @group Rest
 */
class HandlerTest extends \PHPUnit_Framework_TestCase {
	protected $handler;
	
	function setUp() {
		$this->handler = new Handler();
	}
	
	public function tearDown() {
		unset($this->handler);
	}
	
	/**
	 * @medium
	 */
	public function test_json_serialize(){
		$expected = '{"batata":"banana","tomate":"abacate"}';
		$data = array("batata"=>"banana","tomate"=>"abacate");
		$this->assertEquals($expected, $this->handler->serialize('application/json', $data));
	}
	
	public function test_xml_serialize(){
		$expected = "<?xml version=\"1.0\"?>\n<response><array><batata>banana</batata><tomate>abacate</tomate></array></response>\n";
		$data = array("batata"=>"banana","tomate"=>"abacate");
		$this->assertEquals($expected, $this->handler->serialize('application/xml', $data));
	}
}
?>