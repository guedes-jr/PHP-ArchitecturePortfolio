<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost:8001');
define('SITE_NAME', 'Isabella Laurent Architects');

define('DB_HOST', 'localhost');
define('DB_NAME', 'site_arquiteta');
define('DB_USER', 'portfolio_user');
define('DB_PASS', 'portfolio_password');