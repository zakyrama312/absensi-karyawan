<?php
require '../config.php';

// Hancurkan semua data sesi
session_destroy();

// Redirect ke halaman login
header("Location: index.php");
exit();
