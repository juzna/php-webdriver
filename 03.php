<?php
/**
 * Controlling selenium with promises
 */
require_once __DIR__ . '/vendor/autoload.php';


// React services
$eventLoop = React\EventLoop\Factory::create();

$dnsFactory = new React\Dns\Resolver\Factory();
$dnsResolver = $dnsFactory->createCached("8.8.8.8", $eventLoop);

$httpClientFactory = new React\HttpClient\Factory();
$httpClient = $httpClientFactory->create($eventLoop, $dnsResolver);

Flow\Flow::$eventLoop = $eventLoop;


flow(
[
	// coroutine A
	function() {
		$wd = new WebDriver\WebDriver("http://127.0.0.1:4444/wd/hub");
		$session = (yield $wd->session());
		yield $session->open('http://localhost/php-webdriver/page.php');

		$el = (yield $session->element('class name', 'seznam'));
		yield $el->click();

		echo "Done\n";
	},

	// coroutine B
	function() {
		$wd = new WebDriver\WebDriver("http://127.0.0.1:4444/wd/hub");
		$session = (yield $wd->session());
		yield $session->open('http://localhost/php-webdriver/page.php');

		$el = (yield $session->element('class name', 'seznam'));
		yield $el->click();

		echo "Done\n";
	},
]
);


echo "flow finished\n";
