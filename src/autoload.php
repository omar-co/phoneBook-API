<?php

function my_autoloader($class)
{
    $filename = str_replace('\\', '/', $class) . '.php';
    require $filename;
}


spl_autoload_register('my_autoloader');
