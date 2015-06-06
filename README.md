# json2json
JSON to JSON conversion based on a template

This is inspired from JSON2JSON mapper (https://github.com/joelvh/json2json)


#Installation

You can download the latest version by cloning https://github.com/waqqas/json2json.git


# Usage

1. Include the library

```
require("vendor/autoload.php");
use waqqas\json2json\JsonMapper;
```

2. Create mapper object

```
$mapper = new JsonMapper();
```

3. Define template

```
$template = array(
    "path" => "data",
    "as" => array(
        "id" => "_id",
        "lat" => 'coordinates.latitude',
        "long" => 'coordinates.longitude',
        "test" => true,
        "length" => "aboutLength",
        "rand" => "randomNumber",
        "fr" => array(
            "path" => "friends",
            "as" => array(
                "fullName" => "name"
            )
        ),
    )
);

```

4. Get input JSON

```
$input = file_get_contents("input.json");
```

Sample JSON

```
{
  "data": [
    {
      "_id": "55717539ff2959f6cefe81a4",
      "index": 0,
      "guid": "2406c1b4-2347-4f7d-a21f-1f70ad110fe3",
      "isActive": false,
      "balance": "$3,547.05",
      "picture": "http://placehold.it/32x32",
      "age": 36,
      "eyeColor": "blue",
      "name": "Howe Peck",
      "gender": "male",
      "company": "ELITA",
      "email": "howepeck@elita.com",
      "phone": "+1 (966) 411-3339",
      "address": "304 Richards Street, Escondida, Idaho, 414",
      "about": "Pariatur commodo sunt ex officia officia nulla quis minim minim. Ea deserunt tempor nisi minim esse ad. Qui mollit consectetur nisi incididunt eu eiusmod pariatur quis excepteur cupidatat.\r\n",
      "registered": "2014-02-10T10:02:48 -05:00",
      "coordinates": {
        "latitude": 12.335061,
        "longitude": 24.177505
      },
      "tags": [
        "ut",
        "eiusmod",
        "incididunt",
        "velit",
        "minim",
        "deserunt",
        "aliqua"
      ],
      "friends": [
        {
          "id": 0,
          "name": "Whitney Hopkins"
        },
        {
          "id": 1,
          "name": "Caitlin Sandoval"
        },
        {
          "id": 2,
          "name": "Deanne Wagner"
        }
      ],
      "greeting": "Hello, Howe Peck! You have 10 unread messages.",
      "favoriteFruit": "apple"
    },
    {
      "_id": "557175393bc92ea4a21e1554",
      "index": 1,
      "guid": "5732c804-7bdf-4034-8def-08950901fb17",
      "isActive": true,
      "balance": "$2,452.25",
      "picture": "http://placehold.it/32x32",
      "age": 39,
      "eyeColor": "green",
      "name": "Callie Roberson",
      "gender": "female",
      "company": "DIGIGEN",
      "email": "callieroberson@digigen.com",
      "phone": "+1 (833) 577-2433",
      "address": "810 Railroad Avenue, Kingstowne, Utah, 9360",
      "about": "Cupidatat laborum fugiat qui eiusmod irure do minim officia sunt aliquip sint ullamco in. Magna sunt fugiat commodo enim anim laboris ut sunt consequat qui veniam ut exercitation et. Excepteur ea nostrud quis ex laborum ipsum exercitation Lorem. Reprehenderit consequat non irure commodo pariatur enim ea nisi. Amet ullamco mollit qui nulla cillum officia.\r\n",
      "registered": "2015-06-03T09:18:26 -05:00",
      "coordinates": {
        "latitude": -26.688826,
        "longitude": 70.910322
      },
      "tags": [
        "ea",
        "proident",
        "excepteur",
        "nisi",
        "ad",
        "quis",
        "exercitation"
      ],
      "friends": [
        {
          "id": 0,
          "name": "Georgina Daugherty"
        },
        {
          "id": 1,
          "name": "Natalia Madden"
        },
        {
          "id": 2,
          "name": "Sabrina Shields"
        }
      ],
      "greeting": "Hello, Callie Roberson! You have 2 unread messages.",
      "favoriteFruit": "banana"
    }
  ]
}
```

5. Transform input

```
$output = $mapper->transformJson($input, $template);
```

# Supported keywords in template

Following keywords are supported in template

- "path" 
- "as"

# String in template, as value

A sting value in template is processed in following order

1. Name of a static function
2. Name of function on helper class (passed in JsonMapper constructor)
3. Key on current object that is selected by "path"
4. JSONPath string relative to current object
5. Literal string value

# Advanced usage

You can provided a class to the constructor of JsonMapper. This can contain functions that can be specified as string values for mapping. Both static and non-static functions can be specified. Each function is passed two parameters.
1. Current item object
2. Context object

```
class Helper{
    public function ensureFloat($item){
        return (float)$item->value;
    }
}
$mapper = new JsonMapper(new Helper());
```

Context object can be passed as second parameter to constructor.

# "path" usage

path parameter is a JSON-Path string. "$." is not required and added by the library, automatically. Details of JSON-Path is available [here](https://github.com/FlowCommunications/JSONPath).
"." is special character, which specified the root element or "$" as JSON-Path string. "path" is relative to data selected by the parent node. If the parent node is an array, it's relative to each item.


# Caveats

* "path" MUST be specified before the "as", in template
* input should be a valid JSON

