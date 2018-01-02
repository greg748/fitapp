<?php

namespace FitApp\classes;

use ADOConnection;
use FitApp\exceptions\Exception;

/**
 * class DBConnection
 */
class DBConnection {
    private $db_connection;
    protected static $instance;
    final private function __clone() {}

    /**
     * @return static
     */
    public static function get() {
        if ( !(static::$instance instanceof static)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Constructor only gets called once.  This is a singleton pattern to make sure only one connection is created.
     *
     * @param bool
     * @throws Exception
     */
    protected function __construct() {
        $Config = AppConfig::get();
        $this->table_prefix = $Config->getTablePrefix();
        $dsn = $Config->dsn;

        try {
            $this->db_connection = ADONewConnection($dsn);
        }
        catch (\Exception $e) {
            throw new Exception('Failed to connect to dsn', ALERT, $e, ['dsn'=>$dsn]);
        }

        if ($this->db_connection instanceof \ADOConnection) {
            $this->db_connection->SetFetchMode(ADODB_FETCH_ASSOC); // this sets so we only return column names.
        } else {
            throw new Exception("Unable to connect to $dsn", ALERT, null, ['dsn'=>$dsn, 'environment'=>$Config->environment]);
        }
    }

    /**
     * GetADODB
     * Gets the single adodb connection.
     *
     * @return ADOConnection
     */
    function getADODB() {
        return $this->db_connection;
    }
}
