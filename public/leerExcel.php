<?php

require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

$handle=fopen("listaPrecios.csv","r");

while($fileop=fgetcsv($handle,1000,";"))
{
    dump($fileop);
}

