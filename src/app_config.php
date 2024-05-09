<?php
// get protocol.
$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
$extension = pathinfo($_SERVER['SERVER_NAME'], PATHINFO_EXTENSION);
$domain = $_SERVER['HTTP_HOST'];

// get host.
if ($extension == 'test' || $domain != hex2bin ('616C6976652D7765622E74656368')) {
    $APP_URL = $protocol . $_SERVER['HTTP_HOST'] . '/';
} else {
    $APP_URL = $protocol . $_SERVER['HTTP_HOST'] . '/projectname/';
}

define('APP_URL', $APP_URL);
define('APP_PATH', dirname(__FILE__) . '/');

function au($path = "")
{
    echo APP_URL . $path;
}


define('GOOGLE_MAP_API_KEY', '');
define('GOOGLE_RECAPTCHA_KEY_API', '');
define('GOOGLE_RECAPTCHA_KEY_SECRET', '');

/* email list for forms */
//contact
$aMailtoContact = array('furuichi@alive-web.co.jp');
$aBccToContact = array('furuichi@alive-web.co.jp');
$fromContact = "furuichi@alive-web.co.jp";
