<?php

namespace Linko\Managers;

use Linko\Managers\Factories\LogManagerFactory;
use Linko\Models\Log;

/**
 * Description of LogManager
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
class LogManager extends Manager {

    private static $instance;

    public function __construct() {
        self::$instance = $this;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Define Abstract Methods
     * ---------------------------------------------------------------------- */

    public static function getInstance(): Manager {
        if (null === self::$instance) { //constructer haven't be call yet
            self::$instance = LogManagerFactory::create(); // factory construct !
        }
        return self::$instance;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - init
     * ---------------------------------------------------------------------- */

    public function log($logContent, $logCategory = null) {
        $log = new Log();
        if (null !== $logCategory) {
            $log->setCategory($logCategory);
        }
        $log->setContent($logContent);

        $logId = $this->getRepository()->create($log);

        var_dump($logId);
    }

}
