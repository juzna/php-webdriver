<?php
/**
 * Normal blocking selenium
 *
 * WARN: this example won't work, it shows the code without Promises
 * (i.e. usage of the original version)
 */
require_once __DIR__ . '/bootstrap.php';


$wd = new WebDriver\WebDriver("http://127.0.0.1:4444/wd/hub");
$session = $wd->session();
$session->open("$baseUrl/page.php");

$el = $session->element('className', 'seznam');
$el->click();

echo "Done\n";
