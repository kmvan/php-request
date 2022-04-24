<?php

declare(strict_types=1);

use Kmvan\Request\Request;

include \dirname(__DIR__) . '/vendor/autoload.php';

$req = new Request();
$req->setBasicUrl('https://inn-studio.com/api/v1/widget/footer');
var_dump($req->GET());
