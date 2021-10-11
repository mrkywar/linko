<?php

namespace Linko\Serializers;

use Linko\Models\Core\Model;
use Linko\Serializers\Core\SerializerException;
use Linko\Tools\DB\Fields\DBFieldsRetriver;

/**
 * Description of Serializer
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
class Serializer {

    /**
     * 
     * @var string
     */
    private $classModel;

    public function __construct(string $classModel = null) {
        $this->classModel = $classModel;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Serialize & Tools
     * ---------------------------------------------------------------------- */

    /**
     * allow transform Model to Array (DBCompatible)
     * @param array<Model>|Model $items Model(s) to transform
     * @return array
     */
    public function serialize($items) {
        $fields = DBFieldsRetriver::retrive($items);

        if (is_array($items)) {
            $results = [];
            foreach ($items as $key => $item) {
                $results[] = $this->serializeOnce($item, $fields, $key);
            }
            return $results;
        } else {
            return $this->serializeOnce($items, $fields);
        }
    }

    private function serializeOnce(Model $item, $fields, $key = null) {
        $rawData = [];
        foreach ($fields as $field) {
            $getter = "get" . ucfirst($field->getProperty());
            $rawData[$field->getDbName()] = $item->$getter();
        }
        return $rawData;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Unserialize & Tools
     * ---------------------------------------------------------------------- */

    /**
     * allow transform Array (DBCompatible) to Model
     * @param $rawItems to transform
     * @return array<Model>|Model 
     */
    public function unserialize($rawItems) {
        $classModel = $this->classModel;
        if (null === $classModel) {
            throw new SerializerException("No class Model defined");
        } elseif (!is_array($rawItems)) {
            throw new SerializerException("Array expected");
        }
        $fields = DBFieldsRetriver::retrive(new $classModel());

        if (1 === sizeof($rawItems)) {
            return $this->unserializeOnce($rawItems, $fields);
        } else if (is_array($rawItems)) {
            $items = [];
            foreach ($rawItems as $key => $rawItem) {
                $model = $this->unserializeOnce($rawItem, $fields);
                if (null === $model->getId()) {
                    $model->setId($key);
                }
                $items[] = $model;
            }
            return $items;
        }
        return;
    }

    /**
     * Allow you Data To Object transform
     * @param array $rawItem : Raw item to transform
     * @param array<DBField> $fields : Field to use for unserialization
     * @return Model
     */
    private function unserializeOnce($rawItem, $fields) {
        $modelStr = $this->classModel;
        $model = new $modelStr();

        foreach ($fields as $field) {
            if (isset($rawItem[$field->getDbName()])) {
                $setter = "set" . ucfirst($field->getProperty());
                $model->$setter($rawItem[$field->getDbName()]);
            }
        }

        return $model;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Getters & Setters 
     * ---------------------------------------------------------------------- */

    public function getClassModel(): string {
        return $this->classModel;
    }

    public function setClassModel(string $classModel) {
        $this->classModel = $classModel;
        return $this;
    }

}
