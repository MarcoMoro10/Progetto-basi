<?php
    // connect to mongodb
    $m = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    $dbname = 'EformLog';
    $collection = 'Log';
?>