<?php

// Global constants
require_once '../scripts/globals.php';

// Set the "is mobile" session variable
session_start();
$_SESSION['mobile'] = true;

// Go back to the main page. It will check this session variable
header('Location:' . BASE_URL);