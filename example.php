<?php
/**
 *
 *
 */

require_once dirname(__FILE__) . '/ExceptionDumper.php';

try {
    eval("
    throw new Exception('this is error!!!');
        ");
} catch (Exception $e) {
    echo ExceptionDumper::fetch($e);
}
