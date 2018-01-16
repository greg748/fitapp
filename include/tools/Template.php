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
     * <link href=\"https://fonts.googleapis.com/css?family=Amiri|Libre+Baskerville|Oswald|Supermercado+One|Titillium+Web|Ubuntu\" rel=\"stylesheet\"> 
     * @return void
     */
    public static function startPage($title = "Page Title") {
        $start = "
        <html>
        <title>$title</title>
        <head>
        <link href=\"https://fonts.googleapis.com/css?family=Oswald:700|Supermercado+One|Ubuntu\" rel=\"stylesheet\"> 
        <link rel=\"stylesheet\" type=\"text/css\" href=\"/css/basestyles.css\"/>
</head>
<body>
<header>
        <div id=\"logo\"><a href=\"/\">fitapp.mobi</a></div>
        <div id=\"menu\">
        <a href=\"/admin/users/\">Users</a>
        <a href=\"/admin/workouts/\">Workouts</a>
        <a href=\"/admin/exercises/\">Exercises</a>
        <a href=\"/admin/trainers/\">Trainers</a>
        </div>
</header>
<div id=\"content\">
";
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
        echo 
    "</div>
    </body>
    </html>";
    }


}