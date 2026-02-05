<?php
session_start();
session_destroy();
header('Location: /STI_Queuing_System/staff-login');
exit;
?>
