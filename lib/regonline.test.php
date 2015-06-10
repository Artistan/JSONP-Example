<?php

include(dirname(__FILE__).'/regonline.class.php');


$r = new regonline();
$data = $r->get_event(1);

var_dump($data);