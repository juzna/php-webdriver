<?php
/**
 * Controlling selenium with promises
 */
require_once __DIR__ . '/bootstrap.php';


// React services
$eventLoop = React\EventLoop\Factory::create();

$dnsFactory = new React\Dns\Resolver\Factory();
$dnsResolver = $dnsFactory->createCached("8.8.8.8", $eventLoop);

$httpClientFactory = new React\HttpClient\Factory();
$httpClient = $httpClientFactory->create($eventLoop, $dnsResolver);




$wd = new WebDriver\WebDriver("http://127.0.0.1:4444/wd/hub");
$wd->session()
	->then(function(WebDriver\Session $s) use ($baseUrl) {
		return $s->open("$baseUrl/page.php");
	})
	->then(function(WebDriver\Session $s) {
		return $s->element('class name', 'seznam');
	})
	->then(function(\WebDriver\Element $e) {
		return $e->click();
	})
	->then(
		function() {
			echo "Done\n";
			$x = 1;
		},
		function($e) {
			echo "Error\n";
			var_dump($e);
		}
	)
;



$eventLoop->run();
echo "Loop finished\n";
exit;
