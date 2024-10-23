<?php
session_start();
$_SESSION['user'] = true;
$url = '..?op=login&result=ok';
header('Location: ' . $url);