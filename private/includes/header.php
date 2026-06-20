<?php
$basePath = '/SIBDAS/1201707/SGIH/private/assets/';
$geralPath = '/SIBDAS/1201707/SGIH/public/';
$waypath = '/SIBDAS/1201707/SGIH/private/';
require_once 'auth.php';
requireAuthentication();

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGIH - Sistema de Gestão de Inventário Hospitalar</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>fontawesome/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/admin.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/dashboard_admin.css">
</head>
<body>