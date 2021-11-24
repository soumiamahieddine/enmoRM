<?php 

$accountController = \laabs::newController('auth/userAccount');

$account = $accountController->get($event->accountId);

return [
    'account' => $account,
];
