<?php

namespace Linko\Managers;

use Linko\Managers\Core\Manager;
use Linko\Managers\Factories\LoggerFactory;
use Linko\Models\Log;

/**
 * Description of LogManager
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
class Logger extends Manager {

    private static $instance;

    public function __construct() {
        self::$instance = $this;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Define Abstract Methods
     * ---------------------------------------------------------------------- */

    public function buildInstance(): Manager {
        return LoggerFactory::create($this); // factory construct !
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - init
     * ---------------------------------------------------------------------- */

    public function log($logContent, $logCategory = null, $debugMode=false) {
        $log = new Log();
        if (null !== $logCategory) {
            $log->setCategory($logCategory);
        }
        $log->setContent($logContent);

        $logId = $this->getRepository()->setIsDebug($debugMode)->create($log);
        
        return ($this->getRepository()->getById($logId));
    }

    

}
