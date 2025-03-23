<?php defined("_EXEC") or die; ?>
<div class="card border-left-0 rounded-0" style="border-color: #515151">
    <div class="card-body p-2" style="border-left: 5px solid gray;">
        <span><?= $ban->getBanner(); ?> BANNED <?= $ban->getName() ?></span>
        <span class="float-right">
            <?php if ($ban->getTxAdminId() !== null): ?>
                <span>(<?= $ban->getTxAdminId() ?>)</span>
            <?php endif; ?>
            <span>am <?= date("d.m.Y, H:i", $ban->getTimestamp()) ?></span>
        </span><br>
        <span><?= $ban->getReason() ?></span><br>
        <span><small>Läuft am <?= date("d.m.Y, H:i", $ban->getExpiration()) ?> ab</small></span>
    </div>
</div>