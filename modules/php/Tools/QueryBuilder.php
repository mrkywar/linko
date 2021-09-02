<?php

namespace Linko\Tools;

use Linko\Models\Core\Field;
use Linko\Models\Core\Model;
use Linko\Models\Core\QueryString;
use Linko\Tools\Core\FieldValueTransposer as Transposer;

//use Linko\Tools\Core\QueryStatementFacrory as StatementFactory;

/**
 * Description of QueryBuilder
 *
 * @author Mr_Kywar mr_kywar@gmail.com
 */
class QueryBuilder {

    /**
     * @var string
     */
    private $queryType;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $statement;

    /* -------------------------------------------------------------------------
     *                  Properties - Select
     * ---------------------------------------------------------------------- */

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $clauses = [];

    /**
     * @var array
     */
    private $orderBy = [];

    /**
     * @var int
     */
    private $limit;

    /* -------------------------------------------------------------------------
     *                  Properties - Update
     * ---------------------------------------------------------------------- */

    /**
     * @var array
     */
    private $setters = [];

    /* -------------------------------------------------------------------------
     *                  Properties - Create
     * ---------------------------------------------------------------------- */

    /**
     * 
     * @var array
     */
    private $values;

    /* -------------------------------------------------------------------------
     *                  BEGIN - construct
     * ---------------------------------------------------------------------- */

    public function __construct($tableName = null) {
        if (null !== $tableName) {
            $this->tableName = $tableName;
        }
        $this->init();
    }

    private function init() {
        $this->statement = "";
        //-- (re)init select
        $this->orderBy = [];
        $this->limit = null;
        $this->clauses = [];
        $this->fields = [];

        //-- (re)init update
        $this->setters = [];

        //-- (re)init insert
        $this->setters = [];
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Adders & Setters 
     * ---------------------------------------------------------------------- */

    public function select() {
        $this->queryType = QueryString::TYPE_SELECT;
        return $this;
    }

    public function insert() {
        $this->queryType = QueryString::TYPE_INSERT;
        return $this;
    }

    public function update() {
        $this->queryType = QueryString::TYPE_UPDATE;
        return $this;
    }

    public function setTableName(string $tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    public function setFields(array $fields) {
        $this->fields = $fields;
        return $this;
    }

    public function addField(Field $field) {
        $this->fields[$field->getDb()] = "`" . $field->getDb() . "`";
        return $this;
    }

    public function setClauses(array $clauses) {
        $this->clauses = $clauses;
        return $this;
    }

    public function addClause(Field $field, $value) {
        $clause = "`" . $field->getDb() . "`";
        if (is_array($value)) {
            $rawValues = [];
            foreach ($value as $val) {
                $rawValues [] = Transposer::transpose($field, $val);
            }
            $clause .= " IN " . implode(",", Transposer::transpose($field, $rawValues));
        } else {
            $clause .= " = " . implode(",", Transposer::transpose($field, $value));
        }

        $this->clauses[$field->getDb()] = $clause;

        return $this;
    }

    public function setOrderBy(array $orderBy) {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function addOrderBy(Field $field, $dir = QueryString::ORDER_ASC) {
        $this->orderBy[$field->getDb()] = $field->getDb() . " " . $dir;
        return $this;
    }

    public function setLimit(int $limit) {
        $this->limit = $limit;
        return $this;
    }

    public function setSetters(array $setters) {
        $this->setters = $setters;
        return $this;
    }

    public function addSetter(Field $field, $value) {
        $setter = "`" . $field->getDb() . "`";
        $setter .= " = " . Transposer::transpose($field, $value);
        $this->setters[$field->getDb()] = $setter;
        return $this;
    }

    public function setValues(array $values) {
        $this->values = $values;
        return $this;
    }

    public function addValue(Model $model) {
        $rawValue = [];
        foreach ($this->fields as $field) {
            $getter = "get" . $field->getProperty();
            $rawValue[$field->getDb()] = Transposer::transpose($field, $model->$getter());
        }
        $this->values[$model->getId()] = "(" . $rawValue . ")";

        return $this;
    }

    /**
     * WARNING USE WITH CAUTION !!
     * @param string $statement
     * @return $this
     */
    public function setStatement(string $statement) {
        $this->statement = $statement;
        $this->queryType = QueryString::TYPE_CUSTOM;
        return $this;
    }

    /* -------------------------------------------------------------------------
     *                  BEGIN - Getters 
     * ---------------------------------------------------------------------- */

    public function getQueryType(): string {
        return $this->queryType;
    }

    public function getTableName(): string {
        return $this->tableName;
    }

    public function getFields(): array {
        return $this->fields;
    }

    public function getClauses(): array {
        return $this->clauses;
    }

    public function getOrderBy(): array {
        return $this->orderBy;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    public function getSetters(): array {
        return $this->setters;
    }

    public function getValues(): array {
        return $this->values;
    }

    public function getStatement(): string {
        return $this->statement;
    }

}
