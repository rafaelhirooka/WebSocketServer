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

    private $users;

    public function __construct(ConnectionInterface $conn, $users) {
        $this->conn = $conn;
        $this->users = $users;
    }

    public function select(array $command) {
        try {
            switch ($command['command']) {

                case 'subscribe':
                    Manager::table('subscribes')->insert([
                        'subscribe_id' => md5(uniqid(rand(), true)),
                        'connection_id' => $this->conn->resourceId,
                        'channel' => $command['channel']
                    ]);

                    break;

                case 'queue':
                    $clients = Manager::table('subscribes')
                        ->where('channel', $command['channel']);

                    if (isset($command['excludeMe']) && $command['excludeMe'] == 'true')
                        $clients = $clients->where('user', '<>', $this->conn->resourceId);

                    $clients = $clients->get();

                    if ($clients != NULL && !empty($clients)) {
                        foreach ($clients as $client) {
                            $this->users[$client->connection_id]->send($command['message']);
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