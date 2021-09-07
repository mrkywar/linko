<?php
namespace Linko\Managers\Factories;

use Linko\Managers\CardManager;
use Linko\Repository\CardRepository;
use Linko\Repository\FieldsFactories\CardFieldsFactory;
use Linko\Serializers\CardSerializer;

/**
 * Factory to create CardManager objects
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
abstract class CardManagerFactory {

    public static function create() {
        //-- REPOSITORY
        $repository = new CardRepository();
        $repository->setFields(CardFieldsFactory::create($repository));

        //-- SERIALIZER
        $serializer = new CardSerializer();
        $repository->setSerializer($serializer);

        //-- MANAGER
        $manager = new CardManager();
        $manager->setRepository($repository)
                ->setSerializer($serializer);

        return $manager;
    }

}
