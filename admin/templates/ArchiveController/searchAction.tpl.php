<?php defined("_EXEC") or die; ?>
<div class="col-lg-8 mt-4">

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="?controller=archive">Meine Letzten Anträge</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">Nach Identifier Suchen</a>
        </li>
    </ul>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Hier kannst du alle Entbannungsanträge durchsuchen, die jemals geschrieben wurden
    </div>

    <form action="?controller=archive&action=search" method="post">

        <label for="s_discord">Discord ID</label>
        <div class="input-group mb-3">
            <div class="input-group">
                <input id="s_discord" type="number" class="form-control" placeholder="Discord ID des Antragstellers" name="discord" minlength="5">
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">Nach Discord ID suchen</button>
                </div>
            </div>
        </div>

        <label for="s_discord">Bann-ID</label>
        <div class="input-group mb-3">
            <div class="input-group">
                <input id="s_discord" type="text" class="form-control" placeholder="Bann-ID des Antragstellers" name="txAdminId" maxlength="9">
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">Nach Bann-ID suchen</button>
                </div>
            </div>
        </div>

        <label for="s_discord">Steamname</label>
        <div class="input-group mb-3">
            <div class="input-group">
                <input id="s_discord" type="text" class="form-control" placeholder="Steamname des Antragstellers" name="steamName" minlength="1">
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">Nach Steamname suchen</button>
                </div>
            </div>
        </div>

        <label for="s_discord">Antrags-ID</label>
        <div class="input-group mb-3">
            <div class="input-group">
                <input id="s_discord" type="number" class="form-control" placeholder="ID des Entbannungsantrags" name="id" minlength="1">
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">Nach Antrags-ID suchen</button>
                </div>
            </div>
        </div>

    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <?php $count = count($appeals); ?>
        <h4 class="text-success text-center">Suche nach <span class="text-light"><?= $searching ?></span> ergab <?= $count ?> treffer</h4>

        <?php if ($count > 0): ?>
            <p class="text-success text-center">Ergebnisse sortiert nach Erstelldatum</p>
        <?php endif; ?>

        <div id="accordion">
            <?php
            for ($i = count($appeals) - 1; $i >= 0; $i--) {
                printAppealSidePanel($appeals[$i]);
                echo "<p></p>";
            }
            ?>
        </div>
    <?php endif; ?>
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>