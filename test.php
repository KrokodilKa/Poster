<?php
$last_post = ((int)file_get_contents('memory.txt') + 1);
var_dump($last_post);
file_put_contents('memory.txt', $last_post);
$q = file_get_contents('memory.txt');
var_dump($q);