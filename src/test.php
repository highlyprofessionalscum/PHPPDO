<?php

$reflectionClass = new ReflectionClass(\PDO::class);
var_dump($reflectionClass->getConstants());