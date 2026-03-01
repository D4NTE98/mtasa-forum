<?php
require __DIR__ . '/config.php';
session_destroy();
session_start();
flash_set('success', 'Wylogowano.');
header('Location: index.php');
exit;
