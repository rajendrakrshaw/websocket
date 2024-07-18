<?php
namespace App\WebSockets;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class MyWebSocketHandler implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $connection)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($connection);
        echo "New connection! ({$connection->resourceId})\n";
    }

    public function onClose(ConnectionInterface $connection)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($connection);
        echo "Connection {$connection->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $connection->close();
    }

    public function onMessage(ConnectionInterface $connection, MessageInterface $msg)
    {
        // When a message is received, we will broadcast it to all connected clients
        echo "New message from {$connection->resourceId}: {$msg}\n";

        foreach ($this->clients as $client) {
            // The sender is not the receiver, send to each client connected
            if ($connection !== $client) {
                $client->send($msg);
            }
        }
    }
}
