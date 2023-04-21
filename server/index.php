<?php
header('Content-Type: application/json');
sleep(1);
die(json_encode([
    "accessed_on" => date("c")
]));

