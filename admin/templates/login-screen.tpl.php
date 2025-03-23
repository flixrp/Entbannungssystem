<?php defined("_EXEC") or die; ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Verwaltung - Login</title>
    <meta name="robots" content="noindex">
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/include/templates/general-meta.tpl.html"; ?>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="templates/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container" style="max-width: 480px;">
    <img src="/resources/pictures/terminator_pumbgun.gif" alt="terminator pumbgun gif"
            class="rounded img-fluid" style="width: 100%;margin: 40px 0 20px;">
    <div class="jumbotron text-center">
        <h2>AFFEBLÄCHLE!</h2>
        <a target="_blank" href="#" class="btn btn-lg btn-primary"><i class="fab fa-discord"></i> Discord</a>
        <hr>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="name" class="sr-only">Benutzername:</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user-alt"></i></span>
                </div>
                <input type="text" class="form-control input-sm" id="name" name="name" required placeholder="Name" autofocus>
            </div>

            <label for="pwd" class="sr-only">Password:</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                </div>
                <input type="password" class="form-control input-sm" id="pwd" name="password" required placeholder="Passwort">
            </div>

            <div class="form-check mb-3">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="remember-me" value="true">Angemeldet bleiben
                </label>
            </div>
            <button type="submit" class="btn btn-outline-primary" name="login" value="true">Authentifizieren</button>
        </form>
    </div>

    <p class="text-center text-muted mb-5">&copy; 2020-<?= date("Y") ?></p>
</div>

</body>
</html>