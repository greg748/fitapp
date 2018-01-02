<?php

namespace FitApp;

class Autoloader {

    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected $prefixes = [];
    protected $classMap = [];
    protected static $instance;
    protected function __construct() {}

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
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public function register() {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix   The namespace prefix.
     * @param string $base_dir A base directory for class files in the
     *                         namespace.
     * @param bool   $prepend  If true, prepend the base directory to the stack
     *                         instead of appending it; this causes it to be searched first rather
     *                         than last.
     *
     * @return void
     */
    public function addNamespace($prefix, $base_dir, $prepend = FALSE) {
        // normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        // normalize the base directory with a trailing separator
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // initialize the namespace prefix array
        if (isset($this->prefixes[$prefix]) === FALSE) {
            $this->prefixes[$prefix] = [];
        }

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }
    
    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     *
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class) {

        // Check if we have recently loaded this class
        if(isset($this->classMap[$class]) && $this->classMap[$class]){
            if (!class_exists($this->classMap[$class])) {
                require $this->classMap[$class];
            }
            return $this->classMap[$class];
        }
        // the current namespace prefix
        $prefix = $class;

        $relative_class = '';

        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (FALSE !== $pos = strrpos($prefix, '\\')) {

            // retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);

            // the rest is the relative class name
            $relative_class = substr($class, $pos + 1);

            // try to load a mapped file for the prefix and relative class
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);

            // Store files we've found for quick recovery
            if ($mapped_file) {
                $this->classMap[$class] = $mapped_file;

                return $mapped_file;
            }

            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        // Remember that this class does not exist.
        if (!isset($mapped_file) || !$mapped_file) {
            $this->classMap[$class] = false;
        }

        // @todo commented out because of too much noise from WSDL service
        //GelfLogger::publish('Mapped File Not Found', ERROR, 'Autoloader', ['prefix' => $prefix, 'rel_class' => $relative_class])

        // never found a mapped file
        return FALSE;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix         The namespace prefix.
     * @param string $relative_class The relative class name.
     *
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class) {
        // are there any base directories for this namespace prefix?
        if (isset($this->prefixes[$prefix]) === FALSE) {
            return FALSE;
        }

        // look through base directories for this namespace prefix
        foreach ($this->prefixes[$prefix] as $base_dir) {

            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $base_dir
                    . str_replace('\\', ' / ', $relative_class)
                    . '.php';

            // if the mapped file exists, require it
            if ($this->requireFile($file)) {
                // yes, we're done
                return $file;
            } else {
                GelfLogger::publish('Class File Not Found', INFO, 'Autoload', ['filename' => $file, 'prefix' => $prefix, 'rel_class' => $relative_class]);
            }
        }

        // never found it
        return FALSE;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
     *       
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file) {
        if (file_exists($file)) {
            require $file;
            
            return TRUE;
        }
        
        return FALSE;
    
    }
}

$loader = Autoloader::get();

// register the autoloader loadClass method for resolving dependencies
$loader->register();

// register the base directories for the namespace prefix
$BASE_DIR = __DIR__;
$SLASH = DIRECTORY_SEPARATOR;
//$INCLUDE_DIR = $BASE_DIR . $SLASH . 'include' . $SLASH;
$INCLUDE_DIR = $BASE_DIR . $SLASH . 'include' . $SLASH;
$loader->addNamespace('FitApp', $INCLUDE_DIR);
$loader->addNamespace('FitApp', $INCLUDE_DIR);
$loader->addNamespace('FitApp\api', $INCLUDE_DIR . $SLASH . 'api');
$loader->addNamespace('FitApp\exceptions', $INCLUDE_DIR . $SLASH . 'exceptions');
$loader->addNamespace('FitApp\file', $INCLUDE_DIR . $SLASH . 'file');
$loader->addNamespace('FitApp\tests', $BASE_DIR . $SLASH . 'tests');
$loader->addNamespace('FitApp\tools', $INCLUDE_DIR . $SLASH . 'tools');
$loader->addNamespace('FitApp\traits', $INCLUDE_DIR . $SLASH . 'traits');

require_once $INCLUDE_DIR . $SLASH . "tools" . $SLASH . "Functions.php";
// Logging Requires these values Moved here to support Testing purposes
define('ALERT', 1);
define('CRITICAL', 2);
define('ERROR', 3);
define('WARNING', 4);
define('NOTICE', 5);
define('INFO', 6);

// Graylog Error Codes
define('WRITE_ERROR', 111);
