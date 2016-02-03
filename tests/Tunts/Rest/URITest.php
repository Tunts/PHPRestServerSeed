<?php
namespace Tunts\Rest;
/**
 * @group Rest
 */
class URITest extends \PHPUnit_Framework_TestCase {
	public function test_URI_build(){
		$uri = "http://teste/teste/{{teste1}}/{{teste2}}/{{teste3}}";
		$expected = 'http://teste/teste/batata/{{teste2}}/banana?ya=hoo';
		$data = array("teste1"=>"batata","teste3"=>"banana",'ya'=>'hoo');
		$this->assertEquals($expected, new URI($uri, $data));
	}
}
?>