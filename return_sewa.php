<?php
session_start();

// Return actions hanya boleh dilakukan oleh admin melalui admin_return_sewa.php
header('Location: index.php');
exit;
?>