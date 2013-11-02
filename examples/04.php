<?php
/**
 * Controlling selenium with promises
 */
require_once __DIR__ . '/bootstrap.php';


// React services
$eventLoop = new React\EventLoop\StreamSelectLoop; //React\EventLoop\Factory::create();

$dnsFactory = new React\Dns\Resolver\Factory();
$dnsResolver = $dnsFactory->createCached("8.8.8.8", $eventLoop);

$httpClientFactory = new React\HttpClient\Factory();
$httpClient = $httpClientFactory->create($eventLoop, $dnsResolver);


// Web socket
$wsSocket = new React\Socket\Server($eventLoop);
$wsSocket->listen(8083, '0.0.0.0');


class MyApp implements \Ratchet\MessageComponentInterface
{

	function onOpen(\Ratchet\ConnectionInterface $conn)
	{

	}

	function onClose(\Ratchet\ConnectionInterface $conn)
	{
		$x = 1;
	}

	function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
	{

	}

	function onMessage(\Ratchet\ConnectionInterface $from, $msg)
	{
		$x = explode(' ', $msg, 2);
		$verb = $x[0];
		$data = isset($x[1]) ? json_decode($x[1], TRUE) : NULL;

		switch ($verb) {
			case 'run':
				Flow\Flow::add(function() use ($from, $data) {
					$data = (yield $this->sendCommand($from, "ahoj"));
					var_dump($data);
				});
				break;

			case "shutdown":
				global $cWsServer, $wsSocket;
				$wsSocket->shutdown();
				break;

			case 'command-response':
				$commandId = $data['commandId'];
				$p = $this->commands[$commandId];
				$p->resolve($data);
				break;

			default:
				$from->send("what?");
		}
	}


	private $commands = [];
	private function sendCommand($from, $msg)
	{
		$i = count($this->commands);
		$this->commands[$i] = $c = new \React\Promise\Deferred;

		$from->send(json_encode( [ 'commandId' => $i, 'message' => $msg ] ));

		return $c;
	}

}

$cApp = new MyApp;
$cWsServer = new Ratchet\WebSocket\WsServer($cApp);

$ioServer = new \Ratchet\Server\IoServer($cWsServer, $wsSocket, $eventLoop);

$scheduler = new Flow\Schedulers\HorizontalScheduler($eventLoop);
Flow\Flow::register($scheduler);


flow(function() use ($eventLoop, $scheduler) {
	$rp = new ReflectionProperty(\React\EventLoop\StreamSelectLoop::class, 'running');
	$rp->setAccessible(TRUE);
	$rp->setValue($eventLoop, TRUE); // hack

	while($eventLoop->isRunning()) {
		yield Flow\Flow::PASS;
		$eventLoop->tick(TRUE);
		echo date("Y-m-d H:i:s") . " no-op\n";
		// no-op
	}
});


echo "flow finished\n";
