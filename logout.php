<?php

// Include the dependencies
require "login/SSO/SSO.php";
require_once 'app/config.php';

$cas_path = "login/vendor/CAS.php";
SSO\SSO::setCASPath($cas_path);

SSO\SSO::logout(BASEURL);