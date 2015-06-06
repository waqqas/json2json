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

        // Action
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

        // Action
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

        // Action
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

        // Action
        $output = $mapper->transformJson($input, $template);

        // Assert
        $outputArray = json_decode($output, true);

        $this->assertEquals(count($outputArray), 1);
        $this->assertEquals(count($outputArray['level1']), 2);
        $this->assertEquals(array("key1" => "value2"), $outputArray['level1'][0]);
        $this->assertEquals(array("key1" => "value3"), $outputArray['level1'][1]);

    }

    public function testTwoOutputsAtSecondLevel()
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

        // Action
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
}