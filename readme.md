### COMPATIBILITY

- PHP 8.1 or later

### Install

```
composer require kmvan/request
```

### Usage

```
<?php

use Kmvan/Request;

$req = new Request();
$req
    ->setBasicUrl('https://example/path/to') // set url
    ->setQuery([ // set url query
      'age' => '19',
    ])
    ->addHeader('X-Token', '...') // add header
    ->setBody([ // set json post data
      'name' => 'Jack',
    ]);
[
    'status' => $status, // http status code
    'data' => $data, // results json data
    'raw' => $raw, // results string data
    'info' => $info, // curl_getinfo() returns
] = $req->POST(); // create a POST method request
```
