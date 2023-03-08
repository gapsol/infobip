<?php

if ($_REQUEST) {
    $json = file_get_contents('../data/shorten.json');
    $obj = json_decode($json);
    $url_prefix = 'https://shortened.url/?id=';
    $message = '';

    if (isset($_REQUEST['url'])) { // shorten url
        echo 'ORIGINAL 2 SHORTEN<br>';
        echo 'URL: ' . $_REQUEST['url'] . '<br>';
        $rex = "/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/";
        if (!preg_match($rex, $_REQUEST['url'])) {
            http_response_code(400);
            $j['status'] = 'error';
            $j['message'] = 'invalid URL';
            $j['data'] = '';
            print json_encode($j);
            exit;
        }
        // test existing url
        $url_exists = false;
        // get highest id
        $max_id = 0;

        if ($obj == null) {
            $obj = [];
        } else {
            foreach ($obj as $key => $val) {
                if ($val->url == $_REQUEST['url']) {
                    $url_exists = true;
                    $req_id = $key;
                    break;
                }
                if ($val->id > $max_id) {
                    $max_id = $val->id;
                }
            }
        }

        $urlx = $url_exists ? 'TRUE' : 'FALSE';
        echo 'URL EXISTS: ' . $urlx . '<br>';
        echo 'MAX ID: ' . $max_id . '<br>';

        if ($url_exists) {
            // return error
            http_response_code(400);
            $j['status'] = 'error';
            $j['message'] = 'URL already exists';
            $j['data'] = $req_id;
            print json_encode($j);
        } else {
            // create new record
            $js['id'] = ++$max_id;
            $js['url'] = $_REQUEST['url'];
            array_push($obj, $js);
            $json = json_encode($obj);
            $ret = file_put_contents('../data/shorten.json', $json);
            // return new record
            http_response_code(200);
            $j['status'] = 'success';
            $j['message'] = 'new URL added';
            $j['data'] = $url_prefix . $max_id;
            print json_encode($j);
        }
    } else
    if (isset($_REQUEST['surl'])) { // get original url
        echo 'SHORTEN 2 ORIGINAL' . '<br>';
        echo 'sURL: ' . $_REQUEST['surl'] . '<br>';
        // find pair
        $req_id = 0;
        $req_url = '';
        if ($obj !== null) {
            $req_id = substr($_REQUEST['surl'], strlen($url_prefix));
            echo 'REQ_ID: ' . $req_id . '<br>';
            foreach ($obj as $key => $val) {
                if ($req_id == $val->id) {
                    var_dump($val);
                    $req_url = $val->url;
                    echo 'REQ_URL: ' . $req_url . '<br>';
                    break;
                }
            }
        }
        if ($req_url !== '') {
            // return original url
            http_response_code(200);
            $j['status'] = 'success';
            $j['message'] = 'original URL';
            $j['data'] = $req_url;
            print json_encode($j);
        } else {
            http_response_code(400);
            $j['status'] = 'error';
            $j['message'] = 'no URL found';
            $j['data'] = '';
            print json_encode($j);
        }
    } else {
        http_response_code(400);
        $j['status'] = 'error';
        $j['message'] = 'irrelevant request';
        $j['data'] = '';
        print json_encode($j);
    }
} else {
    http_response_code(400);
    $j['status'] = 'error';
    $j['message'] = 'no request';
    $j['data'] = '';
    print json_encode($j);
}
