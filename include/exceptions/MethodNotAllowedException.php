<?php

class MethodNotAlowedException extends Exception {
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);
        error_log($this->getTraceAsString(), 3); //, logfile
    }
}