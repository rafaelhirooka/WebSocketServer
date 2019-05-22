<?php
/**
 * Created by PhpStorm.
 * User: rafael.hirooka
 * Date: 21/05/2019
 * Time: 11:09
 */

namespace App;

use App\Traits\ApiResponser;
use Illuminate\Database\Capsule\Manager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Sender implements MessageComponentInterface
{
    use ApiResponser;

    protected $clients;
    private $subscriptions;
    private $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
    }

    function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
        $this->errorResponse($e->getMessage(), $e->getCode());
    }

    function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
        Manager::table('active_connections')->insert([
            'active_connection_id' => md5(uniqid(rand(), true)),
            'connection_id' => $conn->resourceId
        ]);
    }

    function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);
        Manager::table('active_connections')->where('connection_id', $conn->resourceId)->delete();
        Manager::table('subscribes')->where('connection_id', $conn->resourceId)->delete();
    }

    public function onMessage(ConnectionInterface $conn, $msg) {
        try {
            $msg = is_string($msg) ? (array)json_decode($msg) : (array)$msg;

            $command = new Command($conn, $this->users);
            $command->select($msg);

        } catch (\RuntimeException $e) {
            $this->users[$conn->resourceId]->send($this->errorResponse($e->getMessage(), $e->getCode()));
        } catch (\Exception $e) {
            $this->users[$conn->resourceId]->send($this->errorResponse($e->getMessage(), 400));
        }
    }
}