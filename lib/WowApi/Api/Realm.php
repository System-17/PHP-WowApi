<?php
namespace WowApi\Api;

use WowApi\Exception\ApiException;

class Realm extends Api {

    /**
     * Constants for realm type.
     */
    const TYPE_PVE = 'pve';
    const TYPE_PVP = 'pvp';
    const TYPE_RP = 'rp';
    const TYPE_RPPVP = 'rppvp';

    /**
     * Constants for server population.
     */
    const POP_LOW = 'low';
    const POP_MEDIUM = 'medium';
    const POP_HIGH = 'high';

    /**
     * Constants for server status.
     */
    const STATUS_UP = 1;
    const STATUS_DOWN = 0;

    /**
     * Constants for queue status.
     */
    const QUEUE_YES = 1;
    const QUEUE_NO = 0;

    /**
     * Realm data structure.
     *
     * name <string> - Name of the realm according to region.
     * slug <string> - URL friendly version of the english name.
     * type <enum:string> - Enum mapping of possible types.
     * queue <boolean> - True if the realm currently requires a queue, else false.
     * status <boolean> - True if the realm is up, else false for down.
     * population <enum:string> - Enum mapping of population levels.
     *
     * @access protected
     * @var array
     */
    protected $_schema = array(
        'type' => array(
            self::TYPE_PVE,
            self::TYPE_PVP,
            self::TYPE_RP,
            self::TYPE_RPPVP
        ),
        'queue' => array(
            self::QUEUE_YES,
            self::QUEUE_NO
        ),
        'status' => array(
            self::STATUS_UP,
            self::STATUS_DOWN
        ),
        'population' => array(
            self::POP_LOW,
            self::POP_MEDIUM,
            self::POP_HIGH
        )
    );


    public function getRealm($realm) {
        return $this->getRealms(array($realm));
    }

    /**
     * Get status results for specified realm(s).
     *
     * @param array $realms String or array of realm(s)
     * @return mixed
     */
    public function getRealms(array $realms = array()) {
        if (empty($realms)) {
            return $this->request->get('realm/status');
        } else {
            return $this->request->get('realm/status', array('realms' => implode(',', $realms)));
        }
    }


    /**
     * Get realm(s) based on population level.
     *
     * @access public
     * @param string $population
     * @return array
     */
    public function filterByPopulation($population = self::POP_LOW) {
        if (!in_array($population, $this->schema('population'))) {
            throw new ApiException(sprintf('Invalid population type for %s.', __METHOD__));
        }

        $realms = $this->getRealms();

        return $this->filter(__METHOD__, 'population', $population);
    }


    /**
     * Get realm(s) based on queue status.
     *
     * @access public
     * @param int|string $queue
     * @return array
     */
    public function filterByQueue($queue = self::QUEUE_NO) {
        if (!in_array($queue, $this->schema('queue'))) {
            throw new ApiException(sprintf('Invalid queue status for %s.', __METHOD__));
        }

        $realms = $this->getRealms();

        return $this->filter($realms, 'queue', $queue);
    }

    /**
     * Get realm(s) based on server status.
     *
     * @access public
     * @param int|string $status
     * @return array
     */
    public function filterByStatus($status = self::STATUS_UP) {
        if (!in_array($status, $this->schema('status'))) {
            throw new ApiException(sprintf('Invalid server status for %s.', __METHOD__));
        }

        $realms = $this->getRealms();

        return $this->filter($realms, 'status', $status);
    }

    /**
     * Get realm(s) based on realm type.
     *
     * @access public
     * @param string $type
     * @return array
     */
    public function filterByType($type = self::TYPE_PVP) {
        if (!in_array($type, $this->schema('type'))) {
            throw new ApiException(sprintf('Invalid realm type for %s.', __METHOD__));
        }

        $realms = $this->getRealms();

        return $this->filter($realms, 'type', $type);
    }

    /**
     * Get an array of allowed query parameters
     * @return array
     */
    function getQueryWhitelist() {
        return array('realms');
    }
}
