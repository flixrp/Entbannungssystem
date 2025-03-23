<?php defined("_EXEC") or die; ?>
<div class="col-lg-7 mt-4">
    <h1>Register</h1>
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info alert-dismissable">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-info-circle"></i> Hier kann man Discord-User manuell hinzufügen, sollte der Discord-Bot Offline sein. Der Authentifizierungs-Token des Users, mit dem er dann Entbannungsanträge schreiben kann, wird anschließend hier angezeigt.
            </div>

            <h3>User hinzufügen</h3>

            <form class="form-inline" method="post">
                <div class="form-group mr-2">
                    <label class="sr-only" for="userid">Discord-User-ID:</label>
                    <input type="number" class="form-control" id="userid" placeholder="Discord user ID" name="discord" required>
                </div>
                <button type="submit" class="btn btn-outline-success" formaction="<?= $_SERVER["SCRIPT_NAME"]; ?>?action=registerAddUser&controller=register">Hinzufügen <i class="fas fa-chevron-right"></i></button>
            </form>
        </div>
    </div>
</div>