<?php

namespace tests;

use waqqas\json2json\JsonMapper;


class JsonMapperTest extends \PHPUnit_Framework_TestCase
{

    public function testFirstLevelObject()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "key2",
            )
        );

        $input = '{"key2": "value2"}';

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(array("key1" => "value2"), $outputArray);

    }

    public function testFirstLevelArrayWithOneElement()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "key2",
            )
        );

        $input = '[{"key2": "value2"}]';

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(count($outputArray), 1);
        $this->assertEquals(array("key1" => "value2"), $outputArray[0]);

    }

    public function testFirstLevelArrayWithTwoElements()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "key2",
            )
        );

        $input = '[{"key2": "value2"},{"key2": "value3"}]';

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(count($outputArray), 2);
        $this->assertEquals(array("key1" => "value2"), $outputArray[0]);
        $this->assertEquals(array("key1" => "value3"), $outputArray[1]);

    }

    public function testSecondOutputLevelArray()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "level1" => array(
                "path" => ".",
                "as" => array(
                    "key1" => "key2",
                )
            )
        );

        $input = '[{"key2": "value2"},{"key2": "value3"}]';

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(count($outputArray), 1);
        $this->assertEquals(count($outputArray['level1']), 2);
        $this->assertEquals(array("key1" => "value2"), $outputArray['level1'][0]);
        $this->assertEquals(array("key1" => "value3"), $outputArray['level1'][1]);

    }

    public function testTwoOutputsAtFirstLevel()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "level1" => array(
                "path" => ".",
                "as" => array(
                    "key1" => "key2",
                )
            ),
            "level2" => array(
                "path" => ".",
                "as" => array(
                    "key1" => "key2",
                )
            )
        );

        $input = '[{"key2": "value2"},{"key2": "value3"}]';

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(count($outputArray), 2);
        $this->assertEquals(count($outputArray['level1']), 2);
        $this->assertEquals(array("key1" => "value2"), $outputArray['level1'][0]);
        $this->assertEquals(array("key1" => "value3"), $outputArray['level1'][1]);
        $this->assertEquals(count($outputArray['level2']), 2);
        $this->assertEquals(array("key1" => "value2"), $outputArray['level2'][0]);
        $this->assertEquals(array("key1" => "value3"), $outputArray['level2'][1]);

    }

    public function testOutputAtSecondLevelObject()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "path" => "data1",
            "as" => array(
                "key1" => "key2",
            )
        );

        $input = <<<JSON
{
   "data1":{
      "key2":"value2"
   }
}
JSON;

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);


        $this->assertEquals(array("key1" => "value2"), $outputArray);

    }


    public function testSubObjectMapping()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "key2.subkey",
            )
        );

        $input = <<<JSON
{
   "key2":{
      "subkey":"subvalue"
   }
}
JSON;

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(array("key1" => "subvalue"), $outputArray);

    }

    public function testStringObjectMapping()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "string",
            )
        );

        $input = <<<JSON
{
   "key2": "value2"
}
JSON;

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(array("key1" => "string"), $outputArray);

    }

    public function testNonStaticFunction()
    {
        // Arrange
        $mockHelper = $this->getMock("Helper", array("func1"));
        $mockHelper->expects($this->exactly(1)) // correct number of times
            ->method("func1")            // correct method is called
            ->with((object)array("key2" => "value2"))   // with correct parameters
            ->will($this->returnValue("retval"));   // return a value

        $mapper = new JsonMapper($mockHelper);

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "func1",
            )
        );

        $input = <<<JSON
{
   "key2": "value2"
}
JSON;

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(array("key1" => "retval"), $outputArray); // check that return-value of function is set

    }

    public function testInvalidPathFunction()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = array(
            "path" => ".",
            "as" => array(
                "key1" => "key2[]",
            )
        );

        $input = <<<JSON
{
   "key2": "value2"
}
JSON;

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(array("key1" => "key2[]"), $outputArray); // check that return-value of function is set

    }

    public function testJsonAsTemplate()
    {
        // Arrange
        $mapper = new JsonMapper();

        $template = json_encode(array(
            "path" => ".",
            "as" => array(
                "key1" => "key2",
            )
        ));

        $input = '{"key2": "value2"}';

        // Act
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(array("key1" => "value2"), $outputArray);

    }
}