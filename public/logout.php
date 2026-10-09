<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

if (is_post()) {
    csrf_check();
    do_logout();
}
redirect('index.php');
