<?php
/*ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);*/

require_once "include/register/Register.php";
require_once "include/appeals/Appeals.php";
require_once "include/Recaptcha.php";
require_once "include/Session.php";
require_once "include/functions.inc.php";
require_once "include/Appeal.php";
require_once "include/DiscordBotSync.php";
require_once "include/DiscordWebhook.php";

Session::create();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta property="og:url" content="https://verwaltung.forgerp.net/entbannungsantrag.php">
    <meta property="og:title" content="ForgeRP Entbannungsantrag">
    <meta property="og:description" content="Offizielle Entbannungswebsite von ForgeRP">
    <meta property="og:site_name" content="verwaltung.forgerp.net">
    <meta property="og:image" content="https://img.gta5-mods.com/q75/images/dom-s-1970-dodge-charger-furious-7-add-on-replace-working-blower-custom-dirt/4bce91-35954340943_0c82299128_k.jpg">

    <title>ForgeRP Entbannungsantrag - Formular</title>
    <?php include "include/templates/general-meta.tpl.html"; ?>
    <link rel="stylesheet" href="resources/css/main.css">
    <link rel="stylesheet" href="resources/css/entbannungsantrag.css">
    <link rel="stylesheet" href="resources/css/form-inputs.css">
    <script src="resources/js/entbannungsantrag-utils.js"></script>
</head>
<body>

<!--Content-->
<div id="main" class="content">
    <div class="head">
        <img src="resources/icons/flixrp-icon.jpeg" alt="flixrp icon">
        <h1>ForgeRP</h1>
        <h2>Entbannungsantrag</h2>
    </div>

        <h2>ForgeRP Entbannungsantrag</h2>
            <p>Um einen Entbannungsantrag zu stellen musst du auf unserem <a target="_blank" href="https://discord.forgerp.net">Discord</a> sein!<br><br>

            Discord: <a target="_blank" href="https://discord.com/invite/d5BugGs">https://discord.com/invite/d5BugGs</a><br><br>

            Führe den Befehl '<kbd style="color: gray;">/entbannungsantrag</kbd>' aus. Du bekommst dann vom Bot einen Link, um deinen Entbannungsantrag zu stellen.<br><br>

            Solltest du kein Discord haben kannst du dir hier einen Account erstellen: <a target="_blank" href="https://discord.com/register">https://discord.com/register</a><br><br>

            Mit freundlichen Grüßen,<br>
            Die Projektleitungen
            </p>

</div>

<?php include "include/templates/footer.tpl.php" ?>

</body>
</html>