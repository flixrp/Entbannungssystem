<?php defined("_EXEC") or die; ?>
<div class="col-lg-8 mt-4">
    <?php
    $unansweredAppeals = Appeals::getAppealsByPropertyValue(Appeal::KEY_ANSWER, false, true);
    $unansweredAppealsCount = count($unansweredAppeals);
    if ($unansweredAppealsCount > 0): ?>
        <h1>Anträge</h1>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr class="d-flex">
                    <th class="w-100 border-top-0">Discord User</th>
                    <th class="w-100 border-top-0">Gebannt von</th>
                    <th class="w-100 border-top-0">Gepostet am</th>
                    <th class="w-100 border-top-0 text-right">ID</th>
                </tr>
                </thead>
            </table>
        </div>
    <?php else: ?>
        <div class="jumbotron"><h1>Keine neuen Anträge</h1></div>
    <?php endif; ?>

    <div id="accordion">
        <?php
        for ($i = 0; $i < $unansweredAppealsCount; $i++) {
            printAppealPanel($unansweredAppeals[$i], $i);
        }
        ?>
    </div>
</div>