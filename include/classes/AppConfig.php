<?php
/**
 * Created:
 *   User: mats
 *   Date: 4/29/15
 */

namespace FitApp\classes;

use FitApp\exceptions\Exception;

/**
 * class AppConfig
 */
class AppConfig
{

    const ETL_LIMIT = 1200;

    public $br;
    
    public $app_url;
    
    protected $baseDir;
    protected $config = [];
    protected $is_live = FALSE;
    
    /**
     * @var bool
     */
    private $log_to_file;
    private $pear_directory;
    private $stylesheet = "/style.css";
    private $stylesheet_print = "/style-print.css";
    private $graylog;
    private $base_log_level;
    private $log_dir;

    private function __construct() {
        $this->baseDir = str_replace('include' . DIRECTORY_SEPARATOR . 'classes', '', __DIR__);

        $ini = parse_ini_file($this->baseDir . 'config.ini', TRUE);
        $this->setConfig($ini);

        $env = $ini['environment'];

        if ($env === 'prod') {
            $this->debug = FALSE;
            $this->is_live = TRUE;
        }

        $this->path = $this->app_directory . '/include';
        $this->dsn = $this->fitapp_db;
        $this->br = self::isCommandLine() ? PHP_EOL : '<br/>';
    }

    private function setConfig($c) {
        $this->config = array_merge($this->config, $c);

        foreach ($c as $k => $v) {
            $this->$k = $v;
        }
    }

    public static function isCommandLine() {
        return PHP_SAPI === 'cli';
    }
    
    public static function isPhpUnit() {
        global $argv;
        return strpos($argv[0], 'phpunit') !== FALSE;
    }

    /**
     * GET the singleton
     *
     * @param bool
     *
     * @return self
     */
    public static function get() {
        static $one = NULL;

        if ($one === NULL) {
            $one = new self;
        }

        return $one;
    }

    public function __get($name) {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        } elseif (method_exists($this, "get$name")) {
            return call_user_func([$this, "get$name"]);
        } else {
            return FALSE;
        }
    }

    public function __set($name, $val) {
        if (method_exists($this, "set$name")) {
            call_user_func([$this, "set$name"], $val);
        } else {
            if (!isset($this->config[$name])) {
                $this->config[$name] = $val;
            } else {
                $this->$name = $val;
            }
        }
    }
    
    /**
     * @return mixed
     */
    public function getPearDirectory() {
        return $this->pear_directory;
    }

    /**
     * @return mixed
     */
    public function canLogToFile() {
        return $this->log_to_file;
    }

    /**
     * @return mixed
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * @return mixed
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     * @return mixed
     */
    public function getLogDir() {
        return $this->log_dir;
    }

    /**
     * @return mixed
     */
    public function getTablePrefix() {
        return $this->table_prefix;
    }


    /**
     * @return string
     */
    public function getUploadDirectory() {
        return $this->upload_directory;
    }

    /**
     * @return boolean
     */
    public function isLive() {
        return $this->is_live;
    }

}