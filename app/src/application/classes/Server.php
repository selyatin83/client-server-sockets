<?php
declare(strict_types=1);

namespace Mselyatin\Sockets\application\classes;

use Mselyatin\Sockets\application\classes\SocketAbstract;

class Server extends SocketAbstract
{
    public function start()
    {
        if (
            $this->domain === AF_UNIX
            && file_exists($this->address)
        ) {
            unlink($this->address);
        }

        $this->createSocket();
        $this->bindSocket();
        $this->setAsListener();
        $this->initServer();
    }

    /**
     * @return void
     */
    private function initServer(): void
    {
        echo("Server started..." . PHP_EOL);
        do {
            $incoming = socket_accept($this->socket);
            do {
                $message = socket_read($incoming, 2048);
                echo "Message from client accepted: " . $message . PHP_EOL;
                $messageToClient = "Received " . strlen($message) . " bytes";
                socket_write($incoming, $messageToClient, strlen($messageToClient));
            } while (true);
            socket_close($incoming);
        } while (true);
    }
}