<?php
require_once __DIR__ . '/auth.php';
anshvoid_logout();
header('Location: login.php');
exit;
