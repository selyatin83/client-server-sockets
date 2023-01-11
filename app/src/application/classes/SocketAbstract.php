<?php

namespace Mselyatin\Sockets\application\classes;

use Socket;
use DomainException;
use Mselyatin\Sockets\application\interfaces\BootstrapInterface;

abstract class SocketAbstract implements BootstrapInterface
{
    protected const SOCKET_DIR = "sockets";

    /**
     * @var Socket|null
     */
    protected Socket|null $socket = null;

    /** @var int  */
    protected int $domain = AF_INET;

    /** @var int  */
    protected int $type = SOCK_STREAM;

    /** @var int  */
    protected int $protocol = 0;

    /** @var string  */
    protected string $address;

    /** @var int  */
    protected int $port = 0;

    /**
     * Backlog for socket_listen. Param number two
     * @var int
     */
    protected int $backlog = 1;

    /**
     * @param string $address
     * @param int $domain
     * @param int $type
     * @param int $protocol
     * @param int $port
     */
    public function __construct(
        string $address,
        int $domain,
        int $type,
        int $protocol,
        int $port = 0
    ) {
        $this->address = $address;
        $this->domain = $domain;
        $this->type = $type;
        $this->protocol = $protocol;
        $this->port = $port;
        $this->prepareDirSockets();
    }

    /**
     * @return $this
     */
    public function createSocket(): static
    {
        $socket = socket_create(
            $this->domain,
            $this->type,
            $this->protocol
        );

        if ($socket === false) {
            throw new DomainException('Не удалось создать сокет');
        }

        $this->socket = $socket;
        return $this;
    }

    /**
     * @return $this
     */
    public function bindSocket(): static
    {
        if (!socket_bind($this->socket, $this->address, $this->port)) {
            $this->getLastErrorSocket();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setAsListener(): static
    {
        if (!socket_listen($this->socket, $this->backlog)) {
            $this->getLastErrorSocket();
        }

        return $this;
    }

    /**
     * @return static
     */
    public function isEmptySocket(): static
    {
        if ($this->socket === false) {
            throw new \DomainException('Сокет не может быть пустым');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function closeSocket(): static
    {
        socket_close($this->socket);
        return $this;
    }

    /**
     * @return $this
     */
    public function connectToSocket(): static
    {
        if (!socket_connect($this->socket, $this->address, $this->port)) {
            $this->getLastErrorSocket();
        }

        return $this;
    }

    /**
     * @param string $data
     * @param int|null $length
     * @return string
     */
    public function socketWrite(
        string $data,
        ?int $length = null
    ): string {
        $msg = socket_write($this->socket, $data, $length);
        if ($msg === false) {
            $this->getLastErrorSocket();
        }

        return $msg;
    }

    /**
     * @param int $length
     * @param int $mode
     * @return string|bool
     */
    public function socketRead(
        int $length,
        int $mode = PHP_BINARY_READ
    ): string|bool {
        $msg = socket_read($this->socket, $length, $mode);
        if ($msg === false) {
            $this->getLastErrorSocket();
        }

        return $msg;
    }

    /**
     * @return void
     */
    public function getLastErrorSocket(): void
    {
        $this->isEmptySocket();
        throw new DomainException(
            socket_strerror(
                socket_last_error($this->socket)
            )
        );
    }

    /**
     * @return void
     */
    protected function prepareDirSockets(): void
    {
        if (!file_exists(self::SOCKET_DIR)) {
            mkdir(self::SOCKET_DIR, 0777);
        }
    }
}