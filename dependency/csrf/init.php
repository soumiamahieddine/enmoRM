<?php 
namespace dependency\csrf;

$observer = new CsrfObserver();

\core\Observer\Dispatcher::attach(
    $observer,
    'observeRequest',
    LAABS_REQUEST
);

class CsrfObserver
{
    /**
     * Whitelist for post methods
     * @var array
     */
    protected $whiteList = [];

    /**
     * Observer for user authentication
     * @param \core\Reflection\Command &$servicePath
     * @param array                    &$args
     *
     * @return auth/credential
     *
     * @subject LAABS_REQUEST
     */
    public function observeRequest(&$httpRequest, array &$args = null)
    {
        require_once "../dependency/csrf/libs/csrf/csrfprotector.php";
        \csrfProtector::init();
    }
}