<?php

namespace Lumio\DTO\Database;

class DatabaseCredentials {

    /**
     * driver
     *
     *
     * @var string
     */
    private string $_driver;

    /**
     * host
     *
     *
     * @var string
     */
    private string $_host;

    /**
     * database name
     *
     *
     * @var string
     */
    private string $_dbname;

    /**
     * username
     *
     *
     * @var string
     */
    private string $_username;

    /**
     * password
     *
     *
     * @var string
     */
    private string $_password;

    /**
     * database connection credentials
     *
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param string|null $driver
     */
    public function __construct(
        string $host,
        string $username,
        string $password,
        string $dbname,
        ?string $driver = null
    ) {
        $this->_host = $host;
        $this->_username = $username;
        $this->_password = $password;
        $this->_dbname = $dbname;
        $this->_driver = $driver ?? '';
    }

    /**
     * Get the driver
     *
     *
     * @return string
     */
    public function driver(): string {
        return $this->_driver;
    }

    /**
     * Get the host
     *
     *
     * @return string
     */
    public function host(): string {
        return $this->_host;
    }

    /**
     * Get the database name
     *
     *
     * @return string
     */
    public function dbname(): string {
        return $this->_dbname;
    }

    /**
     * Get the username
     *
     *
     * @return string
     */
    public function username(): string {
        return $this->_username;
    }

    /**
     * Get the password
     *
     *
     * @return string
     */
    public function password(): string {
        return $this->_password;
    }

}
