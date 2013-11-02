<?php
/**
 * Normal blocking selenium
 */
require_once __DIR__ . '/vendor/autoload.php';


$wd = new WebDriver\WebDriver("http://127.0.0.1:4444/wd/hub");
$session = $wd->session();
$session->open('http://localhost/php-webdriver/page.php');

$el = $session->element('className', 'seznam');
$el->click();

echo "Done\n";
