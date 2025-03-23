<p><a href="entbannungsantrag-hilfe.php" target="_blank">Dir ist nicht ganz klar was du eingeben sollst? Erklärung anzeigen</a>
<br>
Oder den Mauszeiger über die Fragezeichen bewegen.
</p>

<form id="unban-form" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input autocomplete="off" type="hidden" tabindex="-1" name="auth2" value="<?= htmlspecialchars($_REQUEST["auth2"]); ?>">

    <div>
        <label for="uf_txadmin_id" id="uf_slider_label">Deine BannID
        </label>
        <div>
            <input oninput="onHeadInput()" id="uf_txadmin_id" name="txAdminId" type="text" value="<?= $inputTxAdmin; ?>" autocomplete="off" maxlength="9">
        </div>
        <?php if (!empty($inputTxAdminErr)): ?>
            <p class="error"><?= $inputTxAdminErr ?></p>
        <?php endif; ?>
        <?php if (!empty($inputSteamnameErr)): ?>
            <p class="error"><?= $inputSteamnameErr ?></p>
        <?php endif;?>
    </div>

    <label for="uf_ban_time">Wann du gebannt wurdest
        <span ondragstart="return false;" class="tooltip">
            <img src="/resources/icons/bx-help-circle.svg" alt="help">
            <span class="tooltiptext">Das Datum, an dem du gebannt wurdest. Diese Information bitte dem Bannscreen entnehmen. Falls dort nicht vorhanden schätzen.</span>
        </span>
    </label>
    <input required name="banTime" type="date" value="<?= $inputBanTime; ?>" autocomplete="off" id="uf_ban_time">
    <?php if (!empty($inputBanTimeErr)): ?>
        <p class="error"><?= $inputBanTimeErr ?></p>
    <?php endif; ?>

    <label for="uf_ban_expire">Bis wann du gebannt wurdest
        <span ondragstart="return false;" class="tooltip">
            <img src="/resources/icons/bx-help-circle.svg" alt="help">
            <span class="tooltiptext">Das Datum, an dem dein Bann abläuft. Diese Information bitte dem Bannscreen entnehmen.</span>
        </span>
    </label>
    <input required name="banExpire" type="date" value="<?= $inputBanExpire; ?>" autocomplete="off" id="uf_ban_expire">
    <?php if (!empty($inputBanExpireErr)): ?>
        <p class="error"><?= $inputBanExpireErr ?></p>
    <?php endif; ?>

    <label for="uf_banned_by">Wer hat dich gebannt
        <span ondragstart="return false;" class="tooltip">
            <img src="/resources/icons/bx-help-circle.svg" alt="help">
            <span class="tooltiptext">Der Supporter, der dich gebannt hat. Diese Information bitte dem Bannscreen entnehmen.</span>
        </span>
    </label>
    <input id="uf_banned_by" required type="text" value="<?= $inputBannedBy; ?>" autocomplete="off" maxlength="60" name="banner">
    <?php if (!empty($inputBannedByErr)): ?>
        <p class="error"><?= $inputBannedByErr ?></p>
    <?php endif; ?>

    <label for="uf_reason">Vom Supporter angegebener Banngrund
        <span ondragstart="return false;" class="tooltip">
            <img src="/resources/icons/bx-help-circle.svg" alt="help">
            <span class="tooltiptext">Der Angegebene Grund. Diese Information bitte dem Bannscreen entnehmen.</span>
        </span>
    </label>
    <textarea id="uf_reason" required spellcheck="false" name="reason" maxlength="2000" rows="1"><?= $inputReason; ?></textarea>
    <?php if (!empty($inputReasonErr)): ?>
        <p class="error"><?= $inputReasonErr ?></p>
    <?php endif; ?>

    <label for="uf_reason_des">Genauere Erklärung des Banngrundes
        <span ondragstart="return false;" class="tooltip">
            <img src="/resources/icons/bx-help-circle.svg" alt="help">
            <span class="tooltiptext">Erläutere, falls nötig, hier den vom Supporter angegebenen Banngrund oder schreibe aus deiner Sichtweise.</span>
        </span>
    </label>
    <textarea id="uf_reason_des" required spellcheck="true" name="reasonDescription" rows="2" maxlength="2000" lang="de"><?= $inputReasonDescr; ?></textarea>
    <?php if (!empty($inputReasonDescrErr)): ?>
        <p class="error"><?= $inputReasonDescrErr ?></p>
    <?php endif; ?>

    <label for="uf_wu">Warum sollen wir dich entbannen?
        <span ondragstart="return false;" class="tooltip">
            <img src="/resources/icons/bx-help-circle.svg" alt="help">
            <span class="tooltiptext">Schreibe hier deinen Entbannungsantrag.</span>
        </span>
    </label>
    <textarea id="uf_wu" required spellcheck="true" name="whyUnban" rows="5" maxlength="2000" lang="de" title="Erkläre warum wir gerade dich entbannen sollen"><?= $inputWhyUnban; ?></textarea>
    <?php if (!empty($inputWhyUnbanErr)): ?>
        <p class="error"><?= $inputWhyUnbanErr ?></p>
    <?php endif; ?>

    <img id="uf_captcha_img" src="/recaptcha_image.php" alt="recaptcha code">
    <input style="background-color: #4c4cc799;" value="Neuen Sicherheitscode laden" type="button" onclick="document.getElementById('uf_captcha_img').src = '/recaptcha_image.php?time=' + new Date();">
    <label for="uf_captcha">Sicherheits Code</label>
    <input id="uf_captcha" autocomplete="off" title="Trage den Sicherheitscode aus dem Bild hier ein" name="captchakey" type="text" minlength="6" maxlength="6" required>
    <?php if (!empty($recaptchaErr)): ?>
        <p class="error"><?= $recaptchaErr ?></p>
    <?php endif; ?>

    <div style="display: flex;align-items: baseline;">
    <input type="checkbox" id="uf_check_no_ai" name="checkbox_no_ai" value="1" required>
        <label for="uf_check_no_ai" style="font-weight: normal;margin-left: 10px !important;">Ich versichere hiermit, dass ich den Entbannungsantrag, <b>ohne Einsatz von KI</b> (wie Beispielsweise ChatGPT), selbst verfasst habe. Mir ist bewusst, dass ich aus dem Entbannungs-System ausgeschlossen werde, sollte ich solche Hilfsmittel benutzt haben.</label>
    </div>

    <p>Durch das Absenden dieses Formulars erklärst du dich mit der <a target="_blank" href="https://verwaltung.forgerp.net/datenschutz.php">Datenschutzerklärung</a> einverstanden. Du versicherst, dass du keine falschen Eingaben gemacht hast und alle Eingaben der Wahrheit entsprechen.</p>

    <input style="margin: 20px 0 0;" type="submit" name="appeal-send" value="Entbannungsantrag absenden" title="Absenden. Obacht, Abgesendet ist Abgesendet! Keine nachträglichen Änderungen möglich.">
</form>