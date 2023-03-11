<?php

class Stats
{
    private $stats = NULL;
    private $stats_file = '../data/stats.json';

    public function stats_load()
    {
        if (file_exists($this->stats_file)) {
            $json = file_get_contents($this->stats_file);
            $this->stats = json_decode($json);
        } else {
            $this->stats->used = 0;
            $this->stats->saved = 0;
            $this->stats->existing = 0;
            $this->stats->invalid = 0;
            $this->stats->redirected = 0;
            $this->stats->notfound = 0;
            $this->stats->badrequest = 0;
            $this->stats_update();
        }
    }

    public function stats_used_update()
    {
        $this->stats->used++;
        $this->stats_update();
    }
    public function stats_saved_update()
    {
        $this->stats->saved++;
        $this->stats_update();
    }
    public function stats_existing_update()
    {
        $this->stats->existing++;
        $this->stats_update();
    }
    public function stats_invalid_update()
    {
        $this->stats->invalid++;
        $this->stats_update();
    }
    public function stats_redirected_update()
    {
        $this->stats->redirected++;
        $this->stats_update();
    }
    public function stats_notfound_update()
    {
        $this->stats->notfound++;
        $this->stats_update();
    }
    public function stats_badrequest_update()
    {
        $this->stats->badrequest++;
        $this->stats_update();
    }

    private function stats_update()
    {
        file_put_contents($this->stats_file, json_encode($this->stats));
    }
}

$stats = new Stats();
$stats->stats_load();

if ($_REQUEST) {
    $stats->stats_used_update();

    $short_list = '../data/shorten.json';
    if (file_exists($short_list)) {
        $json = file_get_contents($short_list);
        $obj = json_decode($json);
    } else {
        file_put_contents($short_list, null);
    }

    $url_array = parse_url(getenv('HTTP_REFERER'));
    $url_base = $url_array['scheme'] . '://' . $url_array['host'];
    $url_prefix = $url_base . '/?id=';
    $message = '';

    if (isset($_REQUEST['url'])) { // shorten url
        // regex for URL validation
        $rex = "/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/";
        if (!preg_match($rex, $_REQUEST['url'])) {
            $stats->stats_invalid_update();

            http_response_code(400);
            $j['status'] = 'error';
            $j['message'] = 'Invalid URL';
            $j['data'] = '';
            print json_encode($j);
            exit;
        }

        $url_exists = false;
        $max_id = 0;
        if ($obj == null) {
            $obj = [];
        } else {
            foreach ($obj as $key => $val) {
                // test existing url
                if ($val->url == $_REQUEST['url']) {
                    $url_exists = true;
                    $req_id = $val->id;
                    break;
                }
                // get highest id
                if ($val->id > $max_id) {
                    $max_id = $val->id;
                }
            }
        }

        if ($url_exists) {
            // return info
            $stats->stats_existing_update();

            http_response_code(200);
            $j['status'] = 'success';
            $j['message'] = 'URL already exists!';
            $j['data'] = $url_prefix . $req_id;
            print json_encode($j);
        } else {
            // create new record
            $js['id'] = ++$max_id;
            $js['url'] = $_REQUEST['url'];
            array_push($obj, $js);
            $json = json_encode($obj);
            $ret = file_put_contents($short_list, $json);
            // return new record
            $stats->stats_saved_update();

            http_response_code(200);
            $j['status'] = 'success';
            $j['message'] = 'New URL added!';
            $j['data'] = $url_prefix . $max_id;
            print json_encode($j);
        }
    } else
    if (isset($_REQUEST['id'])) { // get original url
        // find pair
        $req_url = '';
        if ($obj !== null) {
            foreach ($obj as $key => $val) {
                if ($_REQUEST['id'] == $val->id) {
                    $req_url = $val->url;
                    break;
                }
            }
        }
        if ($req_url !== '') {
            // return original url
            $stats->stats_redirected_update();

            http_response_code(200);
            $j['status'] = 'success';
            $j['message'] = 'Original URL';
            $j['data'] = $req_url;
            print json_encode($j);
        } else {
            $stats->stats_notfound_update();

            http_response_code(400);
            $j['status'] = 'error';
            $j['message'] = 'Redirect for ' . $url_prefix . $_REQUEST['id'] . ' not found!';
            $j['data'] = '';
            print json_encode($j);
        }
    } else {
        $stats->stats_badrequest_update();

        http_response_code(400);
        $j['status'] = 'error';
        $j['message'] = 'Irrelevant request!';
        $j['data'] = '';
        print json_encode($j);
    }
} else {
    $stats->stats_badrequest_update();

    http_response_code(400);
    $j['status'] = 'error';
    $j['message'] = 'No request!';
    $j['data'] = '';
    print json_encode($j);
}
