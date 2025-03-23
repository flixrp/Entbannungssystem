<?php defined("_EXEC") or die; ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta name="description" content="Administration">
    <title>ForgeRP Verwaltung</title>
    <?php include "../include/templates/general-meta.tpl.html"; ?>
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="templates/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="templates/js/resolve-discord-user.js"></script>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</head>
<body>

<!--Header-->
<nav class="navbar navbar-expand-sm bg-primary navbar-dark">
    <a class="navbar-brand pt-0 pb-0" href="index.php">
        <img src="../resources/icons/flixrp-icon.jpeg" alt="Logo" style="width: 40px;">
    </a>
    <a class="navbar-brand" href="index.php">ForgeRP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Entbannungsanträge</a>
            </li>
        </ul>
        <span class="navbar-text mr-1">Eingeloggt als <strong><span class="resolveDiscordUserId"><?= $user->get_discord_id() ?></span></strong></span>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="?action=logout&controller=authentication"><i class="fas fa-sign-out-alt"></i> Ausloggen</a>
            </li>
        </ul>
    </div>
</nav>

<!--Content-->
<div class="container">
    <?= $actionErr ?>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 sidenav p-0">
            <ul class="nav flex-column">
                <li class="nav-item <?php if (CONTROLLER === "appeal") echo "bg-dark"; ?>">
                    <a class="nav-link p-3" href="?controller=appeal"><i class="fas fa-envelope-open"></i> Anträge</a>
                </li>
                <li class="nav-item <?php if (CONTROLLER === "archive") echo "bg-dark"; ?>">
                    <a class="nav-link p-3" href="?controller=archive&action=search"><i class="fas fa-archive"></i> Archiv</a>
                </li>
                <?php if ($user->hasPermission(Login::PERMISSION_ADMIN)): ?>
                    <li class="nav-item <?php if (CONTROLLER === "deniedUser") echo "bg-dark"; ?>">
                        <a class="nav-link p-3" href="?controller=deniedUser"><i class="fas fa-minus-circle"></i> Sperren</a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(Login::PERMISSION_ADMIN)): ?>
                    <li class="nav-item <?php if (CONTROLLER === "authentication") echo "bg-dark"; ?>">
                        <a class="nav-link p-3" href="?controller=authentication"><i class="fas fa-user-friends"></i> Benutzer</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link p-3" target="_blank" href="#"><i class="fas fa-book-open"></i> Team Regelwerk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3" target="_blank" href="#"><i class="fas fa-book-open"></i> Bannzeiten</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3" href="?controller=index&action=socialScore"><i class="fas fa-book-open"></i> Punktesystem – Konzept</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link p-3" href="#"><i class="fas fa-folder-open"></i> Supportermappe</a>
                </li>
            </ul>
        </div>
        <?php require $template; ?>
    </div>
</div>

</body>
</html>