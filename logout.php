<?php
session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy the session
header("Location:/htp2/index.php"); // Redirect to index page
exit();