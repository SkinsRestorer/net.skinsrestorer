<?php
require_once __DIR__ . '/../inc/util/firstload.php';

session_unset();
session_destroy();

header("Location: /panel/login");
exit();
