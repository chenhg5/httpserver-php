<?php
include 'curl.class.php';

function callback($response, $info, $error, $request)
{
    echo str_repeat("-", 50) . "response:\n\n\n";
    print_r($response);
    date_default_timezone_set('UTC');
    echo str_repeat("-", 50) . date("Y-m-d H:i:s") . str_repeat("-", 50) . "\n\n\n";
}

$curl = new Curl ("callback");

$data = [];

for ($i = 1; $i < 3000; $i++) {
    $params = array(
        'url' => 'http://game.leshibaike.com/api/test/login/'.$i, //秦美人
        'method' => 'GET',
        'options' => []
    );
    $data[] = $params;
}

foreach ($data as $val) {
    $request = new Curl_request ($val ['url'], $val ['method']);
    $curl->add($request);
}

$curl->execute();
echo $curl->display_errors();
