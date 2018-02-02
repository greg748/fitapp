<?php

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>FitApp.Mobi</title>
        <link href="https://fonts.googleapis.com/css?family=Oswald:700|Supermercado+One|Ubuntu" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/css/basestyles.css"/>
    </head>
    <body>
        <div id="app"></div>
        <script src="/js/bundle.js"></script>
    </body>

    <style>
        div.exerciseSet {
            border: 1px solid green;
            display: inline-block;
            width: 175px !important;
        }
        div.exerciseSet .exerciseSetType {
            display: inline-block;
        }
        div.exerciseSet .exerciseWeight {
            display: inline;
            white-space: nowrap;
        }
        div.exerciseWeight select {
            display: inline !important;
        }
        div.exerciseSet .exerciseUnits {
            display: inline;
        }
        div.exerciseSet .exerciseReps {
            display: inline;
        }
        input.weightInput, input.repsInput {
            width: 50px;
        }
        div.exerciseSet label {
            width: 60px;
            display: inline-block;
            font-size: 80%;
        }

    </style>
</html>