<?php

require __DIR__ . '/bootstrap/app.php';


/**
 * chat.php
 * Send any incoming messages to all connected clients (except sender)
 */
/*class MyChat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}*/

\Illuminate\Database\Capsule\Manager::table('active_connections')->delete();
\Illuminate\Database\Capsule\Manager::table('subscribes')->delete();

// Run the server application through the WebSocket protocol on port 8080
$app = new Ratchet\App('localhost', 8080);
$app->route('/', new \App\Sender(), array('*'));
$app->run();


/*$server = \Ratchet\Server\IoServer::factory(
    new \App\Sender(),
    8080
);

$server->run();*/