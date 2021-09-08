<?php

namespace Linko\Repository;

use Linko\Adapters\CollectionAdapter;
use Linko\Managers\Deck\Deck;
use Linko\Models\Core\QueryString;
use Linko\Models\Player;
use Linko\Repository\Core\SuperRepository;

/**
 * CardRepository allows you to  manage the Card Model / Data link
 * Call order :
 * [DBRequester] <--> [QueryBuilder] <--> [Repository] <--> [Manager]
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
class CardRepository extends SuperRepository {

    private CONST TABLE_NAME = "card";
    private CONST FIELDS_PREFIX = "card_";

    /* -------------------------------------------------------------------------
     *                  BEGIN - Implement SuperRepository
     * ---------------------------------------------------------------------- */

    public function getFieldsPrefix() {
        return self::FIELDS_PREFIX;
    }

    public function getTableName() {
        return self::TABLE_NAME;
    }

    /* -------------------------------------------------------------------------
     *            BEGIN - Specific Repository Methods
     * ---------------------------------------------------------------------- */


    /**
     * Retrive list of card in a given location
     * @param type $location : Location to be drilled
     * @param type $locationArg : Location Arg to be drilled (optional) if not set Location Arg will be sorted insted
     * @param type $limit : Number of card to get (optional) if not set, get all
     * @return array<Card> : Cards in Location
     */
    public function getCardsInLocation($location, $locationArg = null, $limit = null) {
        $locationField = $this->getFieldByProperty("location");
        $locationArgField = $this->getFieldByProperty("locationArg");

        $qb = $this->getQueryBuilder()
                ->select()
                ->addClause($locationField, $location);
        if (null === $locationArg) {
            $qb->addOrderBy($locationArgField, QueryString::ORDER_DESC);
        } else {
            $qb->addClause($locationArgField, $locationArg);
        }

        if (null !== $limit) {
            $qb->setLimit($limit);
        }

        return $this->execute($qb);
    }

    /**
     * Move Card(s) to specific location
     * @param array<Card> $cards : Card(s) to move 
     * @param mixed $location : location to move to
     * @param mixed $locationArg : location Arg to move to (optional)
     * @return type
     */
    public function moveCardsToLocation($cards, $location, $locationArg = 0) {
        $locationField = $this->getFieldByProperty("location");
        $locationArgField = $this->getFieldByProperty("locationArg");
        $primary = $this->getPrimaryField();

        $qb = $this->getQueryBuilder()
                ->update()
                ->addSetter($locationField, $location)
                ->addSetter($locationArgField, $locationArg);

        if (is_array($cards)) {
            $ids = [];
            foreach ($cards as $card) {
                $ids[] = $card->getId();
            }
            $qb->addClause($primary, $ids);
        } else {
            $qb->addClause($primary, $cards->getId());
        }

        return $this->execute($qb);
    }

    /**
     * Retrive the cards in a player 
     * @param Player $player
     * @return array<Card> : Cards in Location
     */
    public function getPlayerHand(Player $player) {
        return $this->getCardsInLocation(Deck::HAND_NAME, $player->getId(), null);
    }
    
    
    public function getHandsInfos(array $players){
        $results = [];
        
        foreach ($players as $player){
            $results[$player->getId()] = count($this->getPlayerHand($player));
        }     
        
        return $results;
    }
    
    public function getTablesInfos(array $players){
        $results = [];

        foreach ($players as $player){
            $location = Deck::TABLE_NAME."_".$player->getId();
            $cards = $this->getCardsInLocation($location);
            $results[$player->getId()] = CollectionAdapter::adapt($cards);
        }     
        
        return $results;
    }
    
    

}
