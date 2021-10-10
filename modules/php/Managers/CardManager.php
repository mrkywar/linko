<?php

namespace Linko\Managers;

use Linko\Managers\Core\SuperManager;
use Linko\Managers\Deck\Deck;
use Linko\Models\Card;
use Linko\Serializers\Serializer;
use Linko\Tools\DB\DBValueRetriver;
use Linko\Tools\DB\Fields\DBFieldsRetriver;
use Linko\Tools\DB\QueryBuilder;

/**
 * Description of PlayerManager
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
class CardManager extends SuperManager {

    public function initForNewGame(array $options = array()) {
        $deck = new Deck();

        $this->create($deck->getCards());

        $drawCards = $this->getAll(Deck::DRAW_VISIBLE_CARDS);
        $this->moveCards($drawCards, Deck::LOCATION_DRAW);
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Move Methods
     * ---------------------------------------------------------------------- */

    private function moveCards($cards, $destination, $destinationArg = null) {
        $table = $this->getTable($cards);
        $primaries = $this->getPrimaryFields($cards);

        $qb = new QueryBuilder();
        $qb->update()
                ->setTable($table);

        foreach ($primaries as $primary) {
            $qb->addClause($primary, DBValueRetriver::retrive($primary, $cards));
        }

        $fieldLoc = DBFieldsRetriver::retriveFieldByPropertyName("location", $cards);
        $qb->addSetter($fieldLoc, $destination);

        if (null !== $destinationArg) {
            $fieldArg = DBFieldsRetriver::retriveFieldByPropertyName("locationArg", $cards);
            $qb->addSetter($fieldArg, $destinationArg);
        }

        $this->execute($qb);
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Define Abstracts Methods 
     * ---------------------------------------------------------------------- */

    protected function initSerializer(): Serializer {
        return new Serializer(Card::class);
    }

}
