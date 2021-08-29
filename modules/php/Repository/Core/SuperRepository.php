<?php

namespace Linko\Repository\Core;

use Linko\Models\Core\Field;
use Linko\Models\Model;
use Linko\Serializers\Core\Serializer;
use Linko\Tools\ArrayCollection;
use Linko\Tools\QueryBuilder;

/**
 * Description of SuperRepository
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
abstract class SuperRepository implements Repository {

    /**
     * 
     * @var QueryBuilder
     */
    protected $queryBuilder;
    protected $serializer;
    protected $fields;

    /**
     * 
     * @return QueryBuilder
     */
    public function getQueryBuilder() {
        if (null === $this->queryBuilder) {
            $this->queryBuilder = new QueryBuilder($this);
        }
        return $this->queryBuilder;
    }

    /**
     * 
     * @return Serializer
     */
    final public function getSerializer(): Serializer {
        return $this->serializer;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Fields Management
     * ---------------------------------------------------------------------- */

    abstract public function getTableName();

    abstract public function getFieldsPrefix();

    final public function getFields() {
        return $this->fields;
    }

    /**
     * 
     * @return Field
     */
    public function getPrimaryField() {
        $fields = $this->getFields();
        foreach ($fields as $field) {
            if ($field->isPrimary()) {
                return $field;
            }
        }

        return;
    }

    /**
     * get all DBFields
     * @return array all DBFields
     */
    public function getDbFields() {
        $res = [];
        $fields = $this->getFields();
        foreach ($fields as $field) {
            $res [] = $this->getFieldsPrefix() . $field->getProperty();
        }
        return $res;
    }

    /**
     * get all UIFields (usfull for display)
     * @return array all DBFields
     */
    public function getUiFields() {
        $res = new ArrayCollection();
        $fields = $this->getFields();
        foreach ($fields as $field) {
            if ($field->isUi()) {
                $res->add($field);
            }
        }
        return $res;
    }

    /**
     * 
     * @param string $property
     * @return Field
     */
    public function getFieldsByProperty($property) {
        foreach ($this->getFields() as $field) {
            if ($property === $field->getProperty()) {
                return $field;
            }
        }
        return;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Implement Base queries
     * ---------------------------------------------------------------------- */

    public function getAll() {
        return $this->getQueryBuilder()->getAll();
    }

    public function getById($playerId) {
        return $this->getQueryBuilder()->findByPrimary($playerId);
    }

    public function create($items) {
        return $this->getQueryBuilder()->create($items);
    }

    public function update(Model $model) {
        return $this->getQueryBuilder()->update($model);
    }

}
