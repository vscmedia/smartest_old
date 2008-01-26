<?php

if($_SERVER['QUERY_STRING'] && isset($_GET['title'])){
    echo $_GET['title'];
}

?>