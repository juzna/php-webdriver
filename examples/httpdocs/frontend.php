<?php
/**
 * A slow request for testing
 */
sleep(1);
echo json_encode([
	'status' => 0,
	'value' => [
		'get' => $_GET,
		'post' => $_POST,
		'raw' => file_get_contents("php://input"),
	],
]);
