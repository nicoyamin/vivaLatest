<?php
$viva = new medoo([
// required
'database_type' => 'mysql',
'database_name' => 'VIVA',
'server' => 'localhost',
'username' => 'homestead',
'password' => 'secret',
'charset' => 'utf8',
'port' => 3306,
'option' => [
PDO::ATTR_CASE => PDO::CASE_NATURAL
]
]);
