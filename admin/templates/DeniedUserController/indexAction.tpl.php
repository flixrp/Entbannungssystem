<?php defined("_EXEC") or die; ?>
<div class="col-lg-7 mt-4">
    <h1>Sperren</h1>
    <div class="card mb-3">
        <div class="card-body">
            <div class="alert alert-info alert-dismissable">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-info-circle"></i> Hier kann man Discord-User vom Entbannungs-System wieder entsperren.
            </div>

            <h3 class="text-success">User entbannen</h3>

            <form class="form-inline" method="post">
                <div class="form-group mr-2">
                    <label class="sr-only" for="userid">Discord-User-ID:</label>
                    <input type="number" class="form-control" id="userid" placeholder="Discord user ID" name="userid" required>
                </div>
                <button formaction="<?= $_SERVER["SCRIPT_NAME"] ?>?action=unbanUser&controller=deniedUser" type="submit" class="btn btn-outline-success">User entbannen <i class="fas fa-chevron-right"></i></button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info alert-dismissable">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-info-circle"></i> Hier kann man Discord-User vom Entbannungs-System aussperren.
            </div>

            <h3 class="text-danger">User bannen</h3>

            <form method="post">
                <div class="form-group">
                    <label class="sr-only" for="userid">Discord-User-ID:</label>
                    <input type="number" class="form-control input-sm" id="userid" placeholder="Discord user ID" name="userid" required minlength="5">
                </div>
                <div class="form-group">
                    <label for="ban_reason" class="sr-only">Banngrund</label>
                    <textarea name="ban_reason" id="ban_reason" rows="4" placeholder="Grund" class="form-control" maxlength="1000" required></textarea>
                </div>
                <button formaction="<?= $_SERVER["SCRIPT_NAME"] ?>?action=banUser&controller=deniedUser" type="submit" class="btn btn-outline-danger">User bannen <i class="fas fa-chevron-right"></i></button>
            </form>
        </div>
    </div>
</div>