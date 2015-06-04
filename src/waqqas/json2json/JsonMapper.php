<?php

namespace waqqas\json2json;

use Flow\JSONPath\JSONPath;

/**
 * Class JsonMapper
 * JSON 2 JSON conversion library
 * @package waqqas\json2json
 */
class JsonMapper
{

    private $context;

    private $helper;

    function __construct($helper = null, $context = null)
    {
        $this->helper = isset($helper)? $helper: new \StdClass();
        $this->context = $context;
    }


    /**
     * @param $inputJson JSON input string to convert
     * @param $template Template to use for transformation
     * @return string Transformed JSON string
     */
    public function transformJson($inputJson, $template)
    {
        return json_encode($this->transformArray(json_decode($inputJson), $template), JSON_PRETTY_PRINT);
    }


    /**
     * @param $input input to transform
     * @param $template Template to use for transformation
     * @return array Transformed array
     */
    public function transformArray($input, $template)
    {

        $output = array();

        $items = null;

        foreach ($template as $key => $value) {
            switch ($key) {
                case 'path':
                    $items = (new JSONPath($input))->find("$." . $value . ".*")->data();
                    break;
                case 'as':
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            $outputItem = new \StdClass();

                            foreach ($template[$key] as $outputKey => $outputTemplate) {
                                $outputItem->$outputKey = $this->getValue($item, $outputTemplate);

                                array_push($output, $outputItem);
                            }
                        }
                    }
                    break;
                case 'aggregate':
//                    if (is_array($items)) {
//
//                        foreach ($template[$key] as $outputKey => $outputTemplate) {
//                            foreach ($items as $item) {
//                                $this->getValue($item, $template["key"]);
//                            }
//                            $outputItem->$outputKey = array_reduce()
//                        }
//
//                            foreach ($items as $item) {
//                            $outputItem = new \StdClass();
//
//                            foreach ($template[$key] as $outputKey => $outputTemplate) {
//                                $outputItem->$outputKey = $this->getValue($item, $outputTemplate);
//
//                                array_push($output, $outputItem);
//                            }
//                        }
//                    }

                    break;

            }
        }

        return $output;
    }

    public function getValue($item, $outputTemplate){
        $value = null;
        if (is_array($outputTemplate)) {
            $value = $this->transformArray($item, $outputTemplate);

        } else if (is_string($outputTemplate)) {

            if (is_callable($outputTemplate)) {
                $value = call_user_func_array($outputTemplate, array($item, $this->context));
            }
            else if (method_exists($this->helper, $outputTemplate)) {
                $value = call_user_func_array(array($this->helper, $outputTemplate), array($item, $this->context));
            }
            else if (array_key_exists($outputTemplate, $item)) {
                $value = $item->$outputTemplate;
            } else {
                $itemValue = (new JSONPath($item))->find("$." . $outputTemplate)->data();
                $itemValue = $itemValue[0];

                $value = $itemValue;
            }
        }
        else{
            $value = $outputTemplate;
        }

        return $value;
    }
}