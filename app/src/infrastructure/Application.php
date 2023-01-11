<?php

namespace Mselyatin\Sockets\infrastructure;

use Mselyatin\Sockets\application\classes\Client;
use Mselyatin\Sockets\application\classes\Server;
use InvalidArgumentException;
use Mselyatin\Sockets\application\interfaces\BootstrapInterface;

class Application
{
    /** @var array|null  */
    private ?array $argv = [];

    public const MAP_BOOTSTRAP = [
        'server' => Server::class,
        'client' => Client::class
    ];

    /**
     * @param array|null $argv
     */
    public function __construct(?array $argv)
    {
        $this->argv = $argv;
    }

    public function run(): void
    {
        $initiator = $this->argv[1] ?? null;
        if (
            $initiator === null
            || !array_key_exists($initiator, self::MAP_BOOTSTRAP)
        ) {
            throw new InvalidArgumentException(
                "Initiator empty or not supported. Initiator: {{$initiator}}"
            );
        }

        /** @var BootstrapInterface $bootstrap */
        $bootstrap = self::MAP_BOOTSTRAP[$initiator];
        (new $bootstrap(
            "sockets/server.sock",
            AF_UNIX,
            SOCK_STREAM,
            SOL_SOCKET
        ))->start();
    }
}