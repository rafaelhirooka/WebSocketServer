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
    private $secret;

    public function __construct(string $secret) {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
        $this->secret = $secret;
    }

    function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
        $this->errorResponse($e->getMessage(), $e->getCode());
    }

    function onOpen(ConnectionInterface $conn) {
        /*$this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
        Manager::table('active_connections')->insert([
            'active_connection_id' => md5(uniqid(rand(), true)),
            'connection_id' => $conn->resourceId
        ]);*/
    }

    function onClose(ConnectionInterface $conn) {
        /*$this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);
        Manager::table('active_connections')->where('connection_id', $conn->resourceId)->delete();
        Manager::table('subscribes')->where('connection_id', $conn->resourceId)->delete();*/
    }

    public function onMessage(ConnectionInterface $conn, $msg) {
        try {
            $msg = is_string($msg) ? (array)json_decode($msg) : (array)$msg;

            if ($msg != NULL && $msg != false) {
                if (isset($msg['secret']) && $msg['secret'] === $this->secret) {
                    $command = new Command($conn);
                    $command->select($msg, $this->users);
                } else {
                    throw new \RuntimeException('Não autorizado', 401);
                }
            } else {
                throw new \RuntimeException('Mensagem fora do padrão JSON', 422);
            }

        } catch (\RuntimeException $e) {
            $conn->send($this->errorResponse($e->getMessage(), $e->getCode()));
        } catch (\Exception $e) {
            $conn->send($this->errorResponse($e->getMessage(), 400));
        }
    }
}