<?php
/**
 * Created by PhpStorm.
 * User: rafael.hirooka
 * Date: 21/05/2019
 * Time: 11:53
 */

namespace App;

use Illuminate\Database\Capsule\Manager;
use Ratchet\ConnectionInterface;

class Command {

    private $conn;

    public function __construct(ConnectionInterface $conn) {
        $this->conn = $conn;
    }

    public function select(array $command, &$users) {
        try {
            switch ($command['command']) {

                case 'connect':
                    if (isset($command['sessionId']) && !empty($command['sessionId'])) {
                        $r = Manager::table('active_connections')->where(
                            'connection_id', $command['sessionId']
                        )->count();

                        if ($r > 0)
                            $users[$command['sessionId']] = $this->conn;
                        else
                            throw new \RuntimeException('Sessão não é válida. Faça login novamente', 404);

                    } else {
                        $sessionId = md5(uniqid(rand(), true));

                        Manager::table('active_connections')->insert([
                            'active_connection_id' => md5(uniqid(rand(), true)),
                            'connection_id' => $sessionId
                        ]);

                        $users[$sessionId] = $this->conn;

                        $this->conn->send(json_encode(['data' => 'success', 'sessionId' => $sessionId, 'command' => $command['command']]));
                    }

                    break;

                case 'disconnect':

                    Manager::table('active_connections')->where('connection_id', $command['sessionId'])->delete();

                    unset($users[$command['sessionId']]);

                    $this->conn->send(json_encode(['data' => 'success', 'command' => $command['command']]));

                    break;

                case 'subscribe':

                    Manager::table('subscribes')->insert([
                        'subscribe_id' => md5(uniqid(rand(), true)),
                        'connection_id' => $command['sessionId'],
                        'channel' => $command['channel']
                    ]);

                    $this->conn->send(json_encode(['data' => 'success', 'command' => $command['command']]));

                    break;

                case 'unsubscribe':
                    Manager::table('subscribes')->where('connection_id', $command['sessionId'])->delete();

                    $this->conn->send(json_encode(['data' => 'success', 'command' => $command['command']]));

                    break;

                case 'queue':
                    $clients = Manager::table('subscribes')
                        ->where('channel', $command['channel']);

                    if (isset($command['excludeMe']) && $command['excludeMe'] == 'true')
                        $clients = $clients->where('user', '<>', $command['sessionId']);

                    $clients = $clients->get();

                    if ($clients != NULL && !empty($clients)) {
                        foreach ($clients as $client) {
                            $users[$client->connection_id]->send(json_encode(['data' => 'success', 'message' => $command['message'], 'command' => $command['command']]));
                        }
                    }

                    $this->conn->send(json_encode(['data' => 'success', 'command' => $command['command']]));

                    break;

                default:
                    throw new \RuntimeException('Comando não localizado', 404);

                    break;
            }
        } catch (\RuntimeException $e) {
            $this->conn->send(json_encode(['data' => 'error', 'message' => $e->getMessage(), 'code' => $e->getCode()]));
        } catch (\Exception $e) {
            $this->conn->send(json_encode(['data' => 'error', 'message' => $e->getMessage(), 'code' => 400]));
        }
    }
}