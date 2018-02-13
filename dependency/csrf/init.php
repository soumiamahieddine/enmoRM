<?php 
namespace dependency\csrf;

$observer = \laabs::newService("dependency/csrf/CsrfObserver");

\core\Observer\Dispatcher::attach(
    $observer,
    'observeRequest',
    LAABS_REQUEST
);
