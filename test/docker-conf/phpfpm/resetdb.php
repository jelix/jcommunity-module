<?php

echo "Delete all tables from the postgresql database\n";
$tryAgain = true;

while($tryAgain) {

    $cnx = @pg_connect("host='pgsql' port='5432' dbname='jcommunity' user='usertest' password='test1234' ");
    if (!$cnx) {
        echo "  postgresql is not ready yet\n";
        sleep(1);
        continue;
    }
    $tryAgain = false;
    pg_query($cnx, 'drop table if exists jcommunity cascade');
    pg_close($cnx);
}


echo "Delete all tables from the mysql database\n";
$tryAgain = true;

while ($tryAgain) {
    $cnx = @new mysqli("mysql", 'usertest', 'test1234', "jcommunity");
    if ($cnx->connect_errno) {
        throw new Exception('Error during the connection on mysql '.$cnx->connect_errno);
    }

    $tryAgain = false;
    $cnx->query('drop table if exists jcommunity');
    $cnx->close();
}


echo "  tables deleted\n";

