<div id="users">
    <? foreach($users as $user): ?>
        <h3><?= $user->login; ?></h3>
        <br/>
        <strong><?= $user->email; ?></strong>
    <? endforeach ?>
</div>