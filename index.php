<?php

header("X-Frame-Options: sameorigin");

/**
 * @see https://github.com/steampixel/simplePHPRouter/tree/master
 */

require_once "include/functions.inc.php";

class Route {

    private static $routes = [];
    private static $pathNotFound = null;
    private static $methodNotAllowed = null;

    /**
     * Function used to add a new route
     * @param string $expression Route string or expression
     * @param callable $function Function to call if route with allowed method is found
     * @param string|array $method [optional]<br>
     * Either a string of allowed method or an array with string values. Default is the <i>get</i> method
     *
     */
    public static function add($expression, $function, $method = 'get') {
        array_push(self::$routes, [
            'expression' => $expression,
            'function' => $function,
            'method' => $method
        ]);
    }

    public static function getAll() {
        return self::$routes;
    }

    public static function pathNotFound($function) {
        self::$pathNotFound = $function;
    }

    public static function methodNotAllowed($function) {
        self::$methodNotAllowed = $function;
    }

    public static function run($basepath = '', $case_matters = false, $trailing_slash_matters = false, $multimatch = false) {

        // The basepath never needs a trailing slash
        // Because the trailing slash will be added using the route expressions
        $basepath = rtrim($basepath, '/');

        // Parse current URL
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);

        $path = '/';

        // If there is a path available
        if (isset($parsed_url['path'])) {
            // If the trailing slash matters
            if ($trailing_slash_matters) {
                $path = $parsed_url['path'];
            } else {
                // If the path is not equal to the base path (including a trailing slash)
                if ($basepath . '/' != $parsed_url['path']) {
                    // Cut the trailing slash away because it does not matters
                    $path = rtrim($parsed_url['path'], '/');
                } else {
                    $path = $parsed_url['path'];
                }
            }
        }

        $path = urldecode($path);

        // Get current request method
        $method = $_SERVER['REQUEST_METHOD'];

        $path_match_found = false;

        $route_match_found = false;

        foreach (self::$routes as $route) {

            // If the method matches check the path

            // Add basepath to matching string
            if ($basepath != '' && $basepath != '/') {
                $route['expression'] = '(' . $basepath . ')' . $route['expression'];
            }

            // Add 'find string start' automatically
            $route['expression'] = '^' . $route['expression'];

            // Add 'find string end' automatically
            $route['expression'] = $route['expression'] . '$';

            // Check path match
            if (preg_match('#' . $route['expression'] . '#' . ($case_matters ? '' : 'i') . 'u', $path, $matches)) {
                $path_match_found = true;

                // Cast allowed method to array if it's not one already, then run through all methods
                foreach ((array)$route['method'] as $allowedMethod) {
                    // Check method match
                    if (strtolower($method) == strtolower($allowedMethod)) {
                        array_shift($matches); // Always remove first element. This contains the whole string

                        if ($basepath != '' && $basepath != '/') {
                            array_shift($matches); // Remove basepath
                        }

                        if ($return_value = call_user_func_array($route['function'], $matches)) {
                            echo $return_value;
                        }

                        $route_match_found = true;

                        // Do not check other routes
                        break;
                    }
                }
            }

            // Break the loop if the first found route is a match
            if ($route_match_found && !$multimatch) {
                break;
            }

        }

        // No matching route was found
        if (!$route_match_found) {
            // But a matching path exists
            if ($path_match_found) {
                if (self::$methodNotAllowed) {
                    http_response_code(405);
                    call_user_func_array(self::$methodNotAllowed, array($path, $method));
                }
            } else {
                if (self::$pathNotFound) {
                    http_response_code(404);
                    call_user_func_array(self::$pathNotFound, array($path));
                }
            }

        }
    }

}

Route::pathNotFound(function() {
    print '<html lang="de"><head>
    <title>Forgerp.net - 404</title>
    <meta name="description" content="Seite nicht gefunden - Error 404">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="stylesheet" href="/resources/misc/fontawesome5.6.3.all.css">
    <link rel="stylesheet" href="/resources/misc/bootstrap.min.css">
    <link rel="stylesheet" href="/resources/misc/custom.css">
    <script src="/resources/misc/jquery3.5.1.min.js"></script>
    <script src="/resources/misc/popper.min.js"></script>
    <script src="/resources/misc/bootstrap4.5.2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(\'[data-toggle="popover"]\').popover();
            $(\'[data-toggle="tooltip"]\').tooltip();
        });
    </script>
</head>
<body>

<!--Content-->
<div class="jumbotron jumbotron-fluid" style="background-image: url(\'/resources/misc/bc1.jpg\');background-size: cover;background-position: center;">
    <div class="container text-center">
        <h1 class="text-center">Error 404 - Seite nicht gefunden</h1>
        <a href="/" class="btn btn-secondary float-center">Zurück zur Startseite</a>
        <a href="" class="btn btn-secondary float-center">Seite neu laden</a>
    </div>
</div>

<div class="container mb-5">
    Die angeforderte Seite existiert nicht.
</div>

<!--Footer-->
<footer class="footer">
    <div class="container p-3 d-flex flex-wrap">
        <ul class="nav">
            <li class="nav-item"><span class="nav-link text-muted">© 2020-';echo date("Y"); echo '</span></li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.forgerp.net">Website
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="/datenschutz.php">Datenschutzerklärung</a></li>
            <li class="nav-item"><a class="nav-link" href="/impressum.php">Impressum</a></li>
        </ul>
    </div>
</footer>

</body></html>';
});

Route::add("/", function () {
    print '<html lang="de"><head>
    <title>Forgerp.net - Startseite</title>
    <meta name="description" content="Forgerp.net - Forgerp Discord">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="stylesheet" href="/resources/misc/fontawesome5.6.3.all.css">
    <link rel="stylesheet" href="/resources/misc/bootstrap.min.css">
    <link rel="stylesheet" href="/resources/misc/custom.css">
    <script src="/resources/misc/jquery3.5.1.min.js"></script>
    <script src="/resources/misc/popper.min.js"></script>
    <script src="/resources/misc/bootstrap4.5.2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(\'[data-toggle="popover"]\').popover();
            $(\'[data-toggle="tooltip"]\').tooltip();
        });
    </script>
</head>
<body>

<!--Content-->
<div class="jumbotron jumbotron-fluid" style="background-image: url(\'/resources/misc/bc1.jpg\');background-size: cover;background-position: center;">
    <div class="container text-center">
        <h1 class="text-center">ForgeRP - Schmiede dein RP!</h1>
        <a href="/" class="btn btn-secondary">🏠 Startseite</a>
        <a href="/regeln" class="btn btn-primary">📜 Regelwerk</a>
        <a target="_blank" href="https://discord.com/channels/665677622604201993/805754611209076807/805755097875087370" class="btn btn-primary">🕴 Unser Team <i class="fas fa-external-link-alt"></i></a>
        <a href="/entbannungsantrag.php" class="btn btn-primary">🔨 Entbannungsanträge</a>
        <a href="/clearcache" class="btn btn-primary">🗑 FiveM Cache leeren</a>
    </div>
</div>

<div class="container mb-5">
    <!-- <h2>Website</h2> -->
    <div class="float-left">
        GTA V Roleplay Server<br><br>
        Forgerp.net - Schmiede dein RP!<br>
        Direktverbindung <b>forgerp.net</b> oder über die <a target="_blank" href="https://servers.fivem.net/servers/detail/gxdxyo">Serverliste <i class="fas fa-external-link-alt"></i></a><br><br>
        Discord: <a href="https://discord.forgerp.net">https://discord.gg/d5BugGs</a><br><br>

        <a style="background-color:#7289DA "target="_blank" href="https://discord.gg/d5BugGs" class="btn btn-primary">Discord beitreten <i class="fas fa-external-link-alt"></i></a><br>
        <br><br>
    </div>
    <div class="float-right">
        <a style="background-color:#7289DA "target="_blank" href="https://discord.gg/d5BugGs" class="btn btn-block btn-primary">Discord beitreten <i class="fas fa-external-link-alt"></i></a><br>
        <iframe src="https://discord.com/widget?id=665677622604201993&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
    </div>
</div>

<!--Footer-->
<footer class="footer">
    <div class="container p-3 d-flex flex-wrap">
        <ul class="nav">
            <li class="nav-item"><span class="nav-link text-muted">© 2020-';echo date("Y"); echo '</span></li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.forgerp.net">Website
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="/datenschutz.php">Datenschutzerklärung</a></li>
            <li class="nav-item"><a class="nav-link" href="/impressum.php">Impressum</a></li>
        </ul>
    </div>
</footer>

</body></html>';
}, ["get", "post"]);

Route::add("/regeln", function () {
    print '<html lang="de"><head>
    <title>Forgerp.net - Regelwerk</title>
    <meta name="description" content="Forgerp Regelwerk">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="stylesheet" href="/resources/misc/fontawesome5.6.3.all.css">
    <link rel="stylesheet" href="/resources/misc/bootstrap.min.css">
    <link rel="stylesheet" href="/resources/misc/custom.css">
    <script src="/resources/misc/jquery3.5.1.min.js"></script>
    <script src="/resources/misc/popper.min.js"></script>
    <script src="/resources/misc/bootstrap4.5.2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(\'[data-toggle="popover"]\').popover();
            $(\'[data-toggle="tooltip"]\').tooltip();
        });
    </script>
</head>
<body>

<!--Content-->
<div class="jumbotron jumbotron-fluid" style="background-image: url(\'/resources/misc/bc1.jpg\');background-size: cover;background-position: center;">
    <div class="container text-center">
        <h1 class="text-center">ForgeRP - Schmiede dein RP!</h1>
        <a href="/" class="btn btn-primary">🏠 Startseite</a>
        <a href="/regeln" class="btn btn-secondary">📜 Regelwerk</a>
        <a target="_blank" href="https://discord.com/channels/665677622604201993/805754611209076807/805755097875087370" class="btn btn-primary">🕴 Unser Team <i class="fas fa-external-link-alt"></i></a>
        <a href="/entbannungsantrag.php" class="btn btn-primary">🔨 Entbannungsanträge</a>
        <a href="/clearcache" class="btn btn-primary">🗑 FiveM Cache leeren</a>
    </div>
</div>

<div class="container text-center">
    <a href="/forgerp_regelwerk.pdf">Als PDF ansehen</a>
    <br>
    <br>
    <object width="100%" height="1000" type="application/pdf" data="/forgerp_regelwerk.pdf"></object>
</div>

<!--Footer-->
<footer class="footer">
    <div class="container p-3 d-flex flex-wrap">
        <ul class="nav">
            <li class="nav-item"><span class="nav-link text-muted">© 2020-';echo date("Y"); echo '</span></li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.forgerp.net">Website
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="/datenschutz.php">Datenschutzerklärung</a></li>
            <li class="nav-item"><a class="nav-link" href="/impressum.php">Impressum</a></li>
        </ul>
    </div>
</footer>

</body></html>';
}, ["get", "post"]);

Route::add("/clearcache", function () {
    print '<html lang="de"><head>
    <title>Forgerp.net - FiveM Cache leeren</title>
    <meta name="description" content="Forgerp FiveM Cache löschen - FiveM Cache leeren">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="stylesheet" href="/resources/misc/fontawesome5.6.3.all.css">
    <link rel="stylesheet" href="/resources/misc/bootstrap.min.css">
    <link rel="stylesheet" href="/resources/misc/custom.css">
    <script src="/resources/misc/jquery3.5.1.min.js"></script>
    <script src="/resources/misc/popper.min.js"></script>
    <script src="/resources/misc/bootstrap4.5.2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(\'[data-toggle="popover"]\').popover();
            $(\'[data-toggle="tooltip"]\').tooltip();
        });
    </script>
</head>
<body>

<!--Content-->
<div class="jumbotron jumbotron-fluid" style="background-image: url(\'/resources/misc/bc1.jpg\');background-size: cover;background-position: center;">
    <div class="container text-center">
        <h1 class="text-center">ForgeRP - Schmiede dein RP!</h1>
        <a href="/" class="btn btn-primary">🏠 Startseite</a>
        <a href="/regeln" class="btn btn-primary">📜 Regelwerk</a>
        <a target="_blank" href="https://discord.com/channels/665677622604201993/805754611209076807/805755097875087370" class="btn btn-primary">🕴 Unser Team <i class="fas fa-external-link-alt"></i></a>
        <a href="/entbannungsantrag.php" class="btn btn-primary">🔨 Entbannungsanträge</a>
        <a href="/clearcache" class="btn btn-secondary">🗑 FiveM Cache leeren</a>
    </div>
</div>

<div class="container">
    Häufige Frage: Wie leere bzw. lösche ich meinen FiveM Cache?
    <br>
    <br>
    1. Bitte bevor ihr anfangt zu allererst einmal euer Spiel schließen.
    <br>
    <br>
    2. Drücke WIN + R und gebe %appdata% in dem sich öffnnendem Fenster ein. Drücke auf OK<br>
    <img src="/clearcachepictures/appdata_prozent.png"></img>
    <br>
    <br>

    3. Klicke oben in der Navigationsleiste auf AppData und gehe dann in den Ordner Local<br>
    <img src="/clearcachepictures/AppData_Local.png"></img>
    <br>
    <br>

    4. Gehe zu AppData/Local/FiveM/FiveM.app/data/<br>
    <img src="/clearcachepictures/path.png"></img>
    <br>
    <br>

    Du siehst nun folgenden Ordner:<br>
    <img src="/clearcachepictures/data_folder.png"></img>
    <br>
    <br>
    <br>
    <b>Der Ordner "cache"</b>
    <br>
    Hier sind größtenteils Einstellungen aus dem FiveM Hauptmenü, Verknüpfte Konten und Daten und Einstellungen aus der Serverliste gespeichert<br>
    Dieser Ordner muss in der Regel nicht gelöscht werden.
    <br>
    <br>
    <b>Der Ordner "game-storage"</b>
    <br>
    Hier sind Daten und Einstellungen von Rockstar-Games. Diesen Ordner zu löschen ist nur sinnvoll wenn man Fehler im Spiel an sich hat.
Wir haben aber noch nie gehört dass das jemand gebraucht hat.
    Dieser Ordner muss in der Regel nicht gelöscht werden.
    <br>
    <br>
    <b>Der Ordner "nui-storage"</b>
    <br>
    In diesem Ordner werden GUI\'s und Bilder, sowie Icons und ein paar Metadaten von Servern gespeichert.
    Es kann helfen diesen Ordner zu löschen. Er ist in der Regel auch nicht groß.
    <br>
    <br>
    <b>Der Ordner "server-cache" und "server-cache-priv"</b>
    <br>
    In diesem Ordner werden Script-Caches und Stream-Caches von Servern gespeichert. Wenn ein Serverinhaber einen Fehler in Dateien hatte kann es eventuell sein dass in diesem Ordner Fehlerhafte Dateien zwischengespeichert sind.
    Es kann helfen diesen Ordner zu löschen, jedoch müssen dann alle Fahrzeuge und Scripts neu Heruntergeladen werden.
    <br><br>
    Eigentlich braucht mein den Cache inwzischen nicht mehr löschen. Aber diese Anleitung gibt es der Vollständigkeit halber.

    <br>
    <br>

    <a href="/clearcache.png">Anleitung als Bild ansehen</a>

</div>

<!--Footer-->
<footer class="footer">
    <div class="container p-3 d-flex flex-wrap">
        <ul class="nav">
            <li class="nav-item"><span class="nav-link text-muted">© 2020-';echo date("Y"); echo '</span></li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.forgerp.net">Website
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="/datenschutz.php">Datenschutzerklärung</a></li>
            <li class="nav-item"><a class="nav-link" href="/impressum.php">Impressum</a></li>
        </ul>
    </div>
</footer>

</body></html>';
}, ["get", "post"]);

Route::run("/");