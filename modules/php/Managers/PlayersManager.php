<?php

namespace Linko\Managers;

use Linko;
use Linko\Tools\DB_Manager;

/**
 * Players manager : allows to easily access players
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
class PlayersManager extends DB_Manager {

    const CARDS_START = 13;

    private  $serializer;

    //private static $instance;

    public function __construct() {
        $this->serializer = new Linko\Serializers\PlayerSerializer();
        //self::$instance = $this;
    }

    protected function cast($row) {
        return $this->serializer->unserialize($row);
    }

    //-- Abstract definitions (required by DB_Manager)
    protected function getPrimary() {
        return 'player_id';
    }

    protected function getTableName() {
        return 'player';
    }

    //-- For get current game instance
    protected function getGame() {
        return Linko::getInstance();
    }

    function setupNewGame($players, Cards $cardManager, $options) {
        // Create players
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        $queryBuilder = $this->getQueryBuilder()->multipleInsert([
            'player_id',
            'player_color',
            'player_canal',
            'player_name',
            'player_avatar'
        ]);

        $values = [];
        $deck = $cardManager->getDeck();

        foreach ($players as $pId => $player) {
            $color = array_shift($default_colors);
            $canal = $player['player_canal'];
            $avatar = addslashes($player['player_avatar']);
            $name = addslashes($player['player_name']);

            $values[] = [$pId, $color, $canal, $name, $avatar];

            $cards = $deck->pickCards(self::CARDS_START, Cards::LOCATION_DECK, $pId);
            // Notify player about his cards
            self::notifyPlayer($pId, 'newHand', '', array('cards' => $cards));
        }
        $queryBuilder->values($values);
        self::getGame()->reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::getGame()->reloadPlayersBasicInfos();
    }

    /**
     * get All Players from Database
     * @return type
     */
    private function getAll() {
        return $this->getQueryBuilder()->get();
    }

    public function getUiData($pId, CardsManager $cardManager) {
        return array_map(function ($player) use ($pId, $cardManager) {
            return $player->getUiData($pId, $cardManager);
        }, self::getAll());
    }

}