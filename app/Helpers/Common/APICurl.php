<?php

function apiCurl($sql, $values=array()) {
    $client = new \GuzzleHttp\Client();

    $domain = env('API_DOMAIN');
    $token = env('API_TOKEN');

    $value = '';
    foreach($values as $v) {
        //$v = str_replace("'", "\'", $v);
        //$value .= "'".urlencode($v)."',";

        $value .= urlencode($v).', ';
        //$value .= $v.', ';
    }
    $value = trim(trim($value), ',');

    $url = "$domain?token=$token&sql=$sql&value=$value";

    //for testing
    //echo $url; exit;

    $response = $client->get($url)->getBody()->getContents();

    return $response;
}

?>