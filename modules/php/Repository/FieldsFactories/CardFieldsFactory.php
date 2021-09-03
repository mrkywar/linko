<?php

namespace Linko\Repository\FieldsFactories;

use Linko\Models\Core\Field;
use Linko\Repository\Core\Repository;
use Linko\Repository\Core\SuperFieldFactory;

/**
 * Description of PlayerFieldsFactory
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
abstract class CardFieldsFactory extends SuperFieldFactory {

    public static function create(Repository $repo) {
        $fields = [];

        //-- newField($fieldName,$fieldType,$DBprefix = "", $isUi = false,$isPrimary = false)
        $fields[] = self::newField("id", Field::INTEGER_FORMAT, $repo->getFieldsPrefix(), true, true);
        $fields[] = self::newField("type", Field::STRING_FORMAT, $repo->getFieldsPrefix(), true);
        $fields[] = self::newField("location", Field::STRING_FORMAT, $repo->getFieldsPrefix(),true);
        
        $typeArg = self::newField("typeArg", Field::STRING_FORMAT, $repo->getFieldsPrefix(),true);
        $typeArg->setDb("type_arg");
        $fields[] = $typeArg;
        
        $locationArg = self::newField("locationArg", Field::STRING_FORMAT, $repo->getFieldsPrefix(),true);
        $locationArg->setDb("location_arg");
        $fields[] = $locationArg;
         
        return $fields;
    }

}

// private $id;
//    private $type;
//    private $typeArg;
//    private $location;
//    private $locationArg;
//
