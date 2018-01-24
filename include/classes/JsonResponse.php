<?php
namespace Fitapp\classes;

/**
 * Sends a json packet back from the api
 */

class JsonResponse {
    
    protected $content;
    function __construct($packet_array = []) {
        $this->content = $packet_array;

    }

    function __toString() {
        return json_encode($this->content);
    }


}