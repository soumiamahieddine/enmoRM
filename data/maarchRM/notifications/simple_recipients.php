<?php 

$accountController = \laabs::newController('auth/userAccount');
$orgController = \laabs::newController('organization/organization');
$archiveController = \laabs::newController('recordsManagement/archive');


$archive = $archiveController->read($event->objectId);
$originatorUserPositions = $orgController->readUserPositions($archive->originatorOrgRegNumber);
$recipients = [];

foreach ($originatorUserPositions as $originatorUserPosition) {
    $account = $accountController->get($originatorUserPosition->userAccountId);

    $recipients[] = $account->displayName.' <'.$account->emailAddress.'>';
}

return $recipients;
