<?php defined("_EXEC") or die; ?>
<div class="card border-info bg-dark mb-3">
    <div class="card-header p-0 border-0 bg-info text-decoration-none">
        <a class="card-link" data-toggle="collapse" href="#appeal<?= $appealNumber ?>">
            <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                    <tr class="d-flex">
                        <td class="w-100 border-0"><span class="resolveDiscordUserId badge badge-pill badge-primary"><?= $appeal->getDiscord() ?></span></td>
                        <td class="w-100 border-0"><?= $appeal->getBanner() ?></td>
                        <td class="w-100 border-0"><?= date("d.m.Y, H:i", $appeal->getTimestamp()) ?></td>
                        <td class="w-100 border-0 text-right"><?= $appeal->getId() ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </a>
    </div>
    <div id="appeal<?= $appealNumber ?>" class="collapse" data-parent="#accordion">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-9">
                    <h3 title="Angaben des Antragstellers">Antrag</h3>
                    <span class="text-info">Discord: </span><span><span class="resolveDiscordUserId"><?= $appeal->getDiscord() ?></span> (<?= $appeal->getDiscord() ?>)</span><br>
                    <?php if ($appeal->getSteamName()): ?>
                        <span class="text-info">Steam name: </span><span><?= $appeal->getSteamName() ?></span><br>
                    <?php endif; ?>
                    <?php if ($appeal->getTxAdminId()): ?>
                        <span class="text-info">Bann ID: </span><span><?= $appeal->getTxAdminId() ?></span><br>
                    <?php endif; ?>
                    <span class="text-info">Gebannt am: </span><span><?= date("d.m.Y", $appeal->getBanTime()) ?></span><br>
                    <span class="text-info">Gebannt bis: </span><span><?= date("d.m.Y", $appeal->getBanExpire()) ?></span><br>
                    <span class="text-info">Gebannt von: </span><span><?= $appeal->getBanner() ?></span><br>
                    <span class="text-info">Angegebener banngrund:</span><br><span><?= $appeal->getReason() ?></span><br>
                    <span class="text-info">Erklärung des Grundes:</span><br><span><?= $appeal->getReasonDescription() ?></span><br>
                    <span class="text-info">Warum sollen wir dich entbannen?:</span><br><span><?= $appeal->getWhyUnban() ?></span><br>
                </div>
                <div class="col-lg-3">
                    <?php if (count($sameUserAppeals) > 1): ?>
                        <h5>User-Antragsverlauf:</h5>
                        <?php foreach ($sameUserAppeals as $userAppeal): ?>
                            <?php
                            $modalId += 1;
                            if ($userAppeal->getId() === $appeal->getId()) {
                                continue;
                            }
                            ?>
                            <button type="button" class="btn btn-outline-primary mt-1 text-white w-100 font-weight-bold" title="Klicke zum ansehen" data-toggle="modal" data-target="#modal-id_<?= $modalId ?>"><?= coloredHistoryTimeString($userAppeal->getTimestamp()) ?></button>
                            <div id="modal-id_<?= $modalId ?>" class="modal" role="dialog">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <?php printAppealSidePanel($userAppeal) ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">Schließen</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="alert alert-warning alert-dismissible bg-transparent border-warning pt-2 pb-2 pl-2 border mt-2" title="Diese liste bezieht sich auf die nicht-archivierten Entbannungsanträge. Um Auch die Archivierten Entbannungsanträge zu sehen schaue im Archiv!">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <a href="?controller=archive&action=search" class="text-warning text-decoration-none" target="_blank">Suche im Archiv für die vollständige Liste</a>
                        </div>
                    <?php endif; ?>
                    <?php if (count($relatedAppeals) > 1): ?>
                        <div class="alert mt-3 p-2 border border-danger" style="background-color: #ff090926">
                            <h5><?= count($relatedAppeals) ?> verwandte Accounts
                                <span class="fa fa-info-circle" data-toggle="tooltip" title="In seltenen fällen kann das versuchte Bannumgehung sein. Prüfe diese Accounts am besten auch ob Sie auf dem Discord gemutet oder gebannt sind!"></span>
                            </h5>
                            <small>Discord-Accounts die für den gleichen Steamnamen oder die Bann-ID schonmal einen Antrag geschrieben haben</small>
                            <br>
                            <?php foreach ($relatedAppeals as $a): ?>
                                <div>
                                    <span class="badge badge-pill badge-primary"><?= $a->getDiscord() ?></span>
                                    <?= Register::isUserBanned($a->getDiscord()) ? "<span class='badge badge-danger' data-toggle='tooltip' title='User ist vom Entbannungssystem gebannt!'>(gesperrt)</span>" : "" ?>
                                    <br>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($user->hasPermission(Login::PERMISSION_USER)): ?>
                <!-- removed code -->
                <hr>

                <ul class="nav nav-tabs mb-2">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#appeal<?= $appealNumber ?>menu1">Antworten</a></li>
                    <?php if ($user->hasPermission(Login::PERMISSION_MANAGER)): ?>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#appeal<?= $appealNumber ?>menu2">Ignorieren</a></li>
                    <?php endif; ?>

                    <!-- removed code -->
                </ul>

                <div class="tab-content">
                    <div id="appeal<?= $appealNumber ?>menu1" class="tab-pane fade active show">
                        <form method="post">
                            <input type="hidden" tabindex="-1" name="appeal-user" value="<?= $appeal->getDiscord() ?>">
                            <input type="hidden" tabindex="-1" name="appeal-id" value="<?= $appeal->getId() ?>">
                            <div class="form-group">
                                <label class="sr-only" for="message-elm-<?= $appealNumber ?>">Nachricht</label>
                                <textarea id="message-elm-<?= $appealNumber ?>" class="form-control mb-2" autocomplete="off" placeholder="Nachricht" required name="message" style="height:125px" ></textarea>
                                <div class="form-group form-inline">
                                    <div class="btn-group mr-1">
                                        <button type="button" class="btn btn-danger" onclick="document.getElementById('message-elm-<?= $appealNumber ?>').value = '❌ **ABGELEHNT**';">Abgelehnt</button>
                                        <button type="button" class="btn btn-success" onclick="document.getElementById('message-elm-<?= $appealNumber ?>').value = '✅ **ANGENOMMEN**';">Angenommen</button>
                                    </div>

                                    <div class="input-group mr-auto">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="not-date<?= $appealNumber ?>">Verkürtzt</label>
                                        </div>
                                        <input id="not-date<?= $appealNumber ?>" type="date" class="form-control" name="unban-notification-date" placeholder="Datum" onchange="document.getElementById('message-elm-<?= $appealNumber ?>').value = '✅ **VERKÜRTZT** bis zum ' + this.value + ' (YYYY-MM-DD)\nFinde dich zu diesem Datum bitte für deine Entbannung im Support Warteraum ein (Kein Ticket!). Es kann dich dort jeder Supporter entbannen.\nBitte beachte: Die Bannlänge die du beim betreten des Servers siehst wird hierdurch nicht verändert.';">
                                        <div class="input-group-append">
                                            <i class="input-group-text fas fa-calendar-alt"></i>
                                        </div>

                                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('message-elm-<?= $appealNumber ?>').value = 'Die von dir eingegebene BannID konnte nicht gefunden werden.\nDein Entbannungsantrag ist bei uns gespeichert und bis auf deine nächste Antwort als abgearbeitet markiert.\nBitte erstelle einen neuen Antrag mit der korrekten BannID und gebe in die benötigten Felder folgendes ein:\n\Bitte den vorherigen Entbannungsantrag beachten. Korrekter Steam Name oder BannID angegeben\n\nHILFE: https://verwaltung.forgerp.net/entbannungsantrag-hilfe.php';">Ungültige BannID</button>
                                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('message-elm-<?= $appealNumber ?>').value = 'Ich habe dich nicht gebannt, bitte nicht einfach meinen Namen schreiben. Der Entbannungsantrag muss neu abgesendet werden.';">Ich habe dich nicht gebannt</button>
                                    </div>

                                    <div class="input-group">
                                        <button title="Nachricht Abschicken. Der Antragsteller bekommt die Nachricht per Discord zugestellt" class="btn btn-secondary" type="submit" formaction="<?= $_SERVER["SCRIPT_NAME"]?>?action=answerAppeal">Abschicken <i class="fas fa-angle-double-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php if ($user->hasPermission(Login::PERMISSION_MANAGER)): ?>
                        <div id="appeal<?= $appealNumber ?>menu2" class="tab-pane fade">
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Achtung!</strong> Der User
                                wird darüber nicht benachrichtigt. Der Antrag gilt dann als Abgearbeitet und wird hier nicht
                                mehr angezeigt.
                            </div>
                            <form class="form-group form-inline" method="post">
                                <input type="hidden" tabindex="-1" name="appeal-user" value="<?= $appeal->getDiscord() ?>">
                                <button title="Antrag als bearbeitet setzen" class="btn btn-secondary ml-auto" type="submit" formaction="<?= $_SERVER["SCRIPT_NAME"] ?>?action=ignoreAppeal">Antrag als bearbeitet ignorieren <i class="fas fa-angle-double-right"></i></button>
                                <button title="Antrag als bearbeitet setzen und ihn vom Entbannungsantrag-System bannen" class="btn btn-danger ml-1" type="submit" formaction="<?= $_SERVER["SCRIPT_NAME"] ?>?action=ignoreAppealAndBan">Antrag als bearbeitet ignorieren und Bannen <i class="fas fa-angle-double-right"></i></button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- removed code -->

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
