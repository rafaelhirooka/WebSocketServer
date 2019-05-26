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
                        $users[$command['sessionId']] = $this->conn;
                    } else {
                        $sessionId = md5(uniqid(rand(), true));

                        Manager::table('active_connections')->insert([
                            'active_connection_id' => md5(uniqid(rand(), true)),
                            'connection_id' => $sessionId
                        ]);

                        $users[$sessionId] = $this->conn;

                        $this->conn->send(json_encode(['sessionId' => $sessionId]));
                    }

                    break;

                case 'disconnect':

                    Manager::table('active_connections')->where('connection_id', $command['sessionId'])->delete();

                    unset($users[$command['sessionId']]);

                    break;

                case 'subscribe':

                    Manager::table('subscribes')->insert([
                        'subscribe_id' => md5(uniqid(rand(), true)),
                        'connection_id' => $command['sessionId'],
                        'channel' => $command['channel']
                    ]);

                    break;

                case 'unsubscribe':
                    Manager::table('subscribes')->where('connection_id', $command['sessionId'])->delete();

                    break;

                case 'queue':
                    $clients = Manager::table('subscribes')
                        ->where('channel', $command['channel']);

                    if (isset($command['excludeMe']) && $command['excludeMe'] == 'true')
                        $clients = $clients->where('user', '<>', $command['sessionId']);

                    $clients = $clients->get();

                    if ($clients != NULL && !empty($clients)) {
                        foreach ($clients as $client) {
                            $users[$client->connection_id]->send($command['message']);
                        }
                    }

                    break;

                default:
                    throw new \RuntimeException('Comando não localizado', 404);

                    break;
            }
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}