<?php defined("_EXEC") or die; ?>
<div class="card border-light bg-dark">
    <div class="card-header">
        <span class="text-muted">Discord User: </span>
        <span class="resolveDiscordUserId badge badge-primary"><?= $appeal->getDiscord() ?></span>
        <span class="text-muted">, Erstellt am: </span><?= date("d.m.Y, H:i", $appeal->getTimestamp()) ?> (<?= historyTimeString($appeal->getTimestamp()) ?>)
        <span class="text-muted">, ID: </span><?= $appeal->getId() ?>
    </div>
    <div class="card-body">
        <span class="text-info">Discord: </span><span><span class="resolveDiscordUserId"><?= $appeal->getDiscord() ?></span> (<?= $appeal->getDiscord() ?>)</span><br>
        <?php if ($appeal->getSteamName()): ?>
            <span class="text-info">Steam name: </span><span><?= $appeal->getSteamName() ?></span><br>
        <?php endif; ?>
        <?php if ($appeal->getTxAdminId()): ?>
            <span class="text-info">Bann-ID: </span><span><?= $appeal->getTxAdminId() ?></span><br>
        <?php endif; ?>
        <span class="text-info">Gebannt am: </span><span><?= date("d.m.Y", $appeal->getBanTime()) ?></span><br>
        <span class="text-info">Gebannt bis: </span><span><?= date("d.m.Y", $appeal->getBanExpire()) ?></span><br>
        <span class="text-info">Gebannt von: </span><span><?= $appeal->getBanner() ?></span><br>
        <span class="text-info">Angegebener banngrund:</span><br><span><?= $appeal->getReason() ?></span><br>
        <span class="text-info">Erklärung des Grundes:</span><br><span><?= $appeal->getReasonDescription() ?></span><br>
        <span class="text-info">Warum sollen wir dich entbannen?:</span><br><span><?= $appeal->getWhyUnban() ?></span><br>

        <?php if ($appeal->getAnswer() !== false): ?>
            <br><span class="text-muted">Antwort:</span> <?= $appeal->getAnswer() ?><br>
            <span class="text-muted">Antwort von:</span>
            <span class="resolveDiscordUserId"><?= $appeal->getAnswerBy() ?></span><br>
        <?php else: ?>
            <br><span class="text-muted">Antwort austehend...</span><br>
        <?php endif; ?>
    </div>
</div>