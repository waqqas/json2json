<?php

namespace waqqas\json2json;


/**
 * Class JsonMapper
 * JSON 2 JSON conversion library
 * @package waqqas\json2json
 */
class JsonMapper{


    /**
     * @param $inputJson JSON input string to convert
     * @param $template Template to use for transformation
     * @return string Transformed JSON string
     */
    public function transformJson($inputJson, $template){
        return json_encode($this->transformArray(json_decode($inputJson), $template));
    }


    /**
     * @param $inputArray input array to transform
     * @param $template Template to use for transformation
     * @return array Transformed array
     */
    public function transformArray($inputArray, $template){
        return array();

    }

}