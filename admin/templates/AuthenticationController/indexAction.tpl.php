<?php defined("_EXEC") or die; ?>
<div class="col-lg-8 mt-4">
        <h1>Benutzer</h1>
        <p>Benutzer Hinzufügen:</p>
        <form class="form-inline" method="post">
            <label class="sr-only" for="name">Benutzername:</label>
            <input type="text" class="form-control input-sm" id="name" placeholder="Benutzername" name="username" required>
            <label class="sr-only" for="discord">Discord-User-ID:</label>
            <input type="number" class="form-control input-sm" id="discord" placeholder="Discord user ID" name="discord" required>
            <label class="sr-only" for="pwd">Password:</label>
            <input type="text" class="form-control input-sm" id="pwd" placeholder="Passwort" name="password" required>
            <label for="rights" class="sr-only">Rechte:</label>

            <select class="custom-select" id="rights" required name="permission">
                <option value="<?= Login::PERMISSION_VISITOR ?>">Besucher</option>
                <option value="<?= Login::PERMISSION_USER ?>">User</option>
                <option value="<?= Login::PERMISSION_MANAGER ?>">Manager</option>
                <option value="<?= Login::PERMISSION_ADMIN ?>">Admin</option>
            </select>
            <button type="submit" class="btn btn-outline-success" formaction="<?= $_SERVER["SCRIPT_NAME"] ?>?action=addUser&controller=authentication">Hinzufügen</button>
        </form>
        <hr>
        <table class="table table-hover table-dark">
            <thead>
            <tr>
                <th class="border-top-0">ID</th>
                <th class="border-top-0">Name</th>
                <th class="border-top-0">Discord</th>
                <th class="border-top-0">Rolle</th>
                <th class="border-top-0">Status</th>
                <th class="border-top-0"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (Login::getUsers() as $aUser): ?>
                <tr>
                    <td><?= $aUser["id"] ?></td>
                    <td><?= $aUser["username"] ?>
                        <?php if ($aUser["id"] == $user->get_id()): ?>
                            <span class="text-muted"> (Du)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="resolveDiscordUserId"><?= $aUser["discord"] ?></span>
                    </td>
                    <td>
                        <span class="badge badge-primary"><?= Login::parsePermission($aUser["permission"]) ?></span>
                    </td>
                    <?php if ($aUser["logged-in"] == true): ?>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                    logged in
                                </button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-item-text">
                                        <form method="post" class="text-center">
                                            <input type="hidden" name="user-id" value="<?= $aUser["id"] ?>">
                                            <button type="submit" class="btn btn-info btn-sm" formaction="<?= $_SERVER["SCRIPT_NAME"] ?>?action=logoutUser&controller=authentication">Ausloggen</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    <?php else: ?>
                        <td>
                            <button type="button" class="btn btn-danger disabled btn-sm" disabled>logged out</button>
                        </td>
                    <?php endif; ?>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                Aktion
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-item-text">
                                    <form method="post" class="text-center">
                                        <input type="hidden" name="user-id" value="<?= $aUser["id"] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" formaction="<?= $_SERVER["SCRIPT_NAME"] ?>?action=removeUser&controller=authentication">Löschen</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
</div>