<?php
require 'app/Helpers/Env.php';
require 'app/Core/Database.php';
$db = app\Core\Database::getInstance()->getConnection();
$db->query("UPDATE polls SET status = 'Active' WHERE status = 'Draft'");
$stmt = $db->query("SELECT id, title, status, visibility FROM polls");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
