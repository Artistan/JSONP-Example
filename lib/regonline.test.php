<?php

include(dirname(__FILE__).'/regonline.class.php');


$r = new regonline();
$data = $r->get_events();

var_dump($data);