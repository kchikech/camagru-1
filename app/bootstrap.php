<?php

require_once 'config/config.php';
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/mail_helper.php';
require_once 'helpers/image_helper.php';
require_once 'helpers/time_helper.php';
spl_autoload_register(function ($className) {require_once 'lib/'.$className.'.php';});

?>