<?php

namespace Lumio\DTO\Database;

class DatabaseCredentials {

    /**
     * driver
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_driver;

    /**
     * host
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_host;

    /**
     * database name
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_dbname;

    /**
     * username
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_username;

    /**
     * password
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private string $_password;

    /**
     * database connection credentials
     *
     * @author TB
     * @date 27.4.2025
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
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    public function driver(): string {
        return $this->_driver;
    }

    /**
     * Get the host
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    public function host(): string {
        return $this->_host;
    }

    /**
     * Get the database name
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    public function dbname(): string {
        return $this->_dbname;
    }

    /**
     * Get the username
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    public function username(): string {
        return $this->_username;
    }

    /**
     * Get the password
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return string
     */
    public function password(): string {
        return $this->_password;
    }

}
