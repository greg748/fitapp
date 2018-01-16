<?php
/**
 * Provides page start and end template functions
 */
namespace Fitapp\tools;

class Template {

    /**
     * Start the page
     *
     * @param String $title Page Title
     * 
     * @return void
     */
    public static function startPage($title = "Page Title") {
        $start = "
<html>
<title>$title</title>
<head>
<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/basestyles.css\"/>
</head>
<body>";
        echo $start;
    }

    /**
     * End the Page
     * @param String $funcs Functions to run on load
     * @return void
     */
    public static function endPage($funcs = []) {
        $end = "done";
        echo $end;
    }


}