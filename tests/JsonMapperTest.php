<?php

namespace tests;

use waqqas\json2json\JsonMapper;

class JsonMapperTest extends \PHPUnit_Framework_TestCase{

    public function testFirstLevelObject(){
        $mapper = new JsonMapper();

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "key2",
            )
        );

        $input = '{"key2": "value2"}';

        $output = $mapper->transformJson($input, $template);

        $outputArray = json_decode($output, true);

        $this->assertEquals(array("key1" => "value2"), $outputArray);

    }
}