<?php defined("_EXEC") or die; ?>
<div class="col-lg-8 mt-4">

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">Meine Letzten Anträge</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?controller=archive&action=search">Nach Identifier Suchen</a>
        </li>
    </ul>

    <h4 class="text-center text-success">Anträge denen Du geantwortet hast</h4>

    <?php $appealCount = count($appeals) ?>

    <p class="text-center text-success"><?= $appealCount ?> Ergebnisse. Sortiert nach Erstelldatum</p>

    <div id="accordion">
        <?php
        for ($i = $appealCount - 1; $i >= 0; $i--) {

            if ($appealCount < $i - 35) {
                //break;
            }

            printAppealSidePanel($appeals[$i]);
            echo "<p></p>";
        }
        ?>
    </div>
</div>