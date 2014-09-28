<?php
namespace Christiaan\SftpIndexer;

/**
 * SftpServer
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class SftpServer
{
    private $name;
    private $host;
    private $port;
    private $user;
    private $password;

    /**
     * @param string $name
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     */
    public function __construct($name, $host, $port, $user, $password)
    {
        $this->name = $name;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
