<?php

namespace waqqas\json2json;

use Flow\JSONPath\JSONPath;
use Flow\JSONPath\JSONPathException;

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
        $this->helper = isset($helper) ? $helper : new \StdClass();
        $this->context = $context;
    }


    /**
     * @param $inputJson string input string to convert
     * @param $template array to use for transformation
     * @return string Transformed JSON string
     */
    public function transformJson($inputJson, $template)
    {
        if( is_string($template))
            $template = json_decode($template, true);
        return json_encode($this->transformArray(json_decode($inputJson), $template));
    }


    /**
     * @param $input array to transform
     * @param $template array to use for transformation
     * @return array Transformed array
     */
    public function transformArray($input, $template)
    {

        $output = new \StdClass();

        $items = null;

        foreach ($template as $key => $value) {
            switch ($key) {
                case 'path':
                    ($value == '.')? $value = '$': $value = "$." . $value;

                    $items = (new JSONPath($input))->find($value)->data();
                    $items = $items[0];

                    break;
                case 'as':

                    if (is_array($items)) {
                        $output = array();
                        foreach ($items as $item) {
                            $outputItem = new \StdClass();

                            foreach ($template[$key] as $outputKey => $outputTemplate) {
                                $outputItem->$outputKey = $this->getValue($item, $outputTemplate);
                            }
                            array_push($output, $outputItem);
                        }
                    } else if (is_object($items)) {
                        foreach ($template[$key] as $outputKey => $outputTemplate) {
                            $output->$outputKey = $this->getValue($items, $outputTemplate);
                        }
                    }
                    break;
                default:
                    if( is_array($value)){
                        $output->$key = $this->getValue($input, $value);
                    }

            }
        }

        return $output;
    }

    public function getValue($item, $outputTemplate)
    {
        $value = null;
        if (is_array($outputTemplate)) {
            $value = $this->transformArray($item, $outputTemplate);

        } else if (is_string($outputTemplate)) {

            if (is_callable($outputTemplate)) {
                $value = call_user_func_array($outputTemplate, array($item, $this->context));
            } else if (method_exists($this->helper, $outputTemplate)) {
                $value = call_user_func_array(array($this->helper, $outputTemplate), array($item, $this->context));
            } else if (array_key_exists($outputTemplate, $item)) {
                $value = $item->$outputTemplate;

            } // check if not empty to ensure valid expression
            else{
                $path = (($outputTemplate == '.')? '$': "$." . $outputTemplate);
                try {
                    $jsonPath = new JSONPath($item);

                    $itemValue = $jsonPath->find($path)->data();
                    if (!empty($itemValue)) {
                        $value = $itemValue[0];
                    } else {
                        $value = $outputTemplate;
                    }
                }
                catch(JSONPathException $e){
                    $value = $outputTemplate;
                }
            }
        } // non string values copied as-is
        else {
            $value = $outputTemplate;
        }

        return $value;
    }
}