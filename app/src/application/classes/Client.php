<?php
declare(strict_types=1);

namespace Mselyatin\Sockets\application\classes;

use Mselyatin\Sockets\application\classes\SocketAbstract;

class Client extends SocketAbstract
{
    public function start()
    {
        $this->createSocket();
        $this->connectToSocket();
        $this->initClient();
    }

    /**
     * @return void
     */
    private function initClient(): void
    {
        do {
            echo("####################################################" . PHP_EOL);
            echo("----------------------------------------------------" . PHP_EOL);
            echo('Write a anything' . PHP_EOL);
            echo("----------------------------------------------------" . PHP_EOL);
            $message = trim(fgets(STDIN));
            echo("Your text: {{$message}}" . PHP_EOL);
            $this->socketWrite($message, strlen($message));
            $messageFromServer = $this->socketRead(2048);
            echo("Answer from server: $messageFromServer" . PHP_EOL);
            echo("####################################################" . PHP_EOL);
        } while (true);
    }
}