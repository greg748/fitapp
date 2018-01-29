<?php
namespace Fitapp\classes;

use ADOConnection;
use ADORecordSet;
use Fitapp\exceptions\Exception;

class Table {
    /**
     *
     * @var ADOConnection
     */
    protected $db;
    protected $table_prefix;
    protected $table_name;
    protected $pkey;
    protected $insert_id;
    protected $last_sql;
    protected $error_msg;
    protected $is_saved = FALSE;
    protected $usesObjectTable = FALSE;

    protected $problem_fields;
    protected $null_fields = [];
    protected $array_fields = [];
    protected $json_fields = [];
    
    //Table Fields
    protected $fields = [];
    protected $no_insert = [];
    protected $no_save = [];

    /**
     * Constructor
     *
     * Initializes the db, table prefix, and the list of fields associated with the table.
     * If having problem with child/grandchild classes, be sure to put parent::__construct() at end of child's constructor
     */
    protected function __construct() {
        $this->db = DBConnection::get()->getADODB();
        $this->table_prefix = AppConfig::get()->getTablePrefix();
        
        if (isset($this->table_prefix_override)) {
            $this->table_prefix = $this->table_prefix_override;
        }
        $this->setNullFields();
    
    }

    /**
     * Store which fields should be passed as nulls instead of ''
     * Separated from constructor so it's called with the right context
     */
    protected function setNullFields() {
        foreach ($this->fields as $key=>$value) {
            if (is_null($value)) {
                $this->null_fields[] = $key;
            }
        }
    
    }

    /**
     * Creates a new table row with the values pass into the fields array.
     *
     * @param array $fields
     * @param bool $show_sql
     *
     * @return static
     * @throws \Fitapp\exceptions\Exception
     */
    public static function create($fields, $show_sql = FALSE) {
        $obj = static::getNewSelf();
        $obj->setFields($fields);
        if (isset($fields['show_sql'])) {
            $show_sql = TRUE;
        }
        $insert_id = $obj->newInsert($show_sql);
        
        if (!$obj->usesObjectTable && isset($obj->pkey)) {
            $obj->setField($obj->pkey, $insert_id);
        }
        
        return $obj;
    
    }
 
    /**
     * Used by the array map for array items
     *
     * @param mixed $val
     * @return void
     */
    protected function sqlArrayQuote($val) {
        if (trim($val) == '' || is_numeric($val)) {
            return $val;
        } else {
            return '"' . addslashes($val). '"';
        }
    }

    /**
     * Inserts a new task record into the database with the current internal data.
     * Returns the id of the new record.
     *
     * @param bool $show_sql
     *
     * @throws Exception
     * @return integer
     */
    protected function newInsert($show_sql = FALSE) {
        $object_fields = $this->getFields();
        
        $queryNames = [];
        $queryValues = [];
        foreach ($object_fields as $name=>$value) {
            if (in_array($name, $this->no_insert)) { // excluded fields
                continue;
            }
            
            $queryNames[] = $name;
            if (is_null($value)) {
                $queryValues[] = "NULL";
            } else {
                if (in_array($name, $this->array_fields)) {
                    $queryValues[] = "'{" . implode(',', array_map([$this, "sqlArrayQuote"], array_filter($value))) . "}'";
                } elseif (in_array($name, $this->json_fields)) {
                    $queryValues[] = ($value) ?: "'{}'";
                } else {
                    if (!is_numeric($value) && strlen($value) > 0) {
                        $value = trim($value);
                    }
                    $queryValues[] = "{$this->db->qstr($value)}";
                }
            }
        }
        
        //put in a false value just in case this is the first time the db connection has been used.
        $insertId = $this->db->Insert_ID() == '' ? 'none' : $this->db->Insert_ID();
        
        $sql = "INSERT INTO {$this->table_prefix}{$this->table_name} (" . implode(', ', $queryNames) . ") VALUES (" . implode(', ', $queryValues) . ")";
        if (isset($this->pkey)) {
            $sql .= " RETURNING {$this->pkey}";
        }
        if ($show_sql) {
            echo $sql;
        }
        $result = $this->db->execute($sql);
        
        $this->last_sql = $sql;
        if ($this->db->ErrorMsg() != '') {
            $this->error_msg = $this->db->ErrorMsg();
            if ($show_sql) {
                echo "\n{$this->error_msg}";
            }
            error_log("SQL error {$this->error_msg}\n$sql");
        }
        
        //check that the insert id has changed to show that the insert was successful
        if (!$result) {
            echo $this->db->errorMsg();
            echo "\n".$this->lastSql();
            $this->insert_id = NULL;
            // throw new Exception('Failed to Insert Object', CRITICAL, NULL, ['sql' => $this->lastSql(), 'full_message' => $this->db->ErrorMsg()])
        } else {
            foreach ($result as $r) {
                $this->insert_id = $r[$this->pkey]; // we return the result
            }
        }
        
        if ($show_sql) {
            echo "\n".$sql;
        }
        
        return $this->insert_id;
    
    }

    /**
     * Get Fields
     *
     * Returns an array representing all the fields in the table
     *
     * @return array
     */
    public function getFields() {
        $current = $this->fields;
        $object_fields = [];
        
        //Loop through each field using the getfield function so that
        //specific fields functions can be overridden in children classes
        foreach ($current as $name=>$value) {
            $object_fields[$name] = $this->getField($name);
        }
        
        return $object_fields;
    
    }

    /**
     * Set Fields
     *
     * Updates the fields array with the supplied values. If a supplied field does not exist in the current fields
     * array it will not be added.
     * The table will not be updated with the new value until save is called.
     *
     * @param array|ADORecordSet $fields
     */
    function setFields($fields) {
        foreach ($fields as $name=>$value) {
            if (is_numeric($name)) {
                continue;
            }
            if (!in_array($name, $this->array_fields) && !is_numeric($value) && is_string($value) && strlen($value) > 0) {
                $value = trim($value);
            }
            $this->setField($name, $value);
        }
    
    }

    /**
     * Get Field
     *
     * Returns the current value of one of the table fields. Cannot be used to access non-table fields.
     * Automatically calles getter functions that match the field name.
     * For example if $name = project_id get Field will call the getProjectId function if it exists.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getField($name) {
        if (is_numeric($name)) {
            return FALSE;
        }
        
        $trans = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $method = "get$trans";
        
        if (in_array($method, get_class_methods($this))) {
            return $this->$method();
        } elseif (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
    
    }

    /**
     * Set Field
     *
     * Updates the field array with the supplied value. If a supplied field does not exist in the current fields array
     * it will not be added.
     *
     * The table will not be updated with the new value until save() is called.
     *
     * Automatically calls setter functions that match the field name.
     * For example if $name = project_id get Field will call the setProjectId function if it exists.
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return mixed
     */
    function setField($name, $value) {
        if (is_numeric($name)) {
            return TRUE;
        }
        
        if (array_key_exists($name, $this->fields)) {
            
            $trans = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
            $method = "set$trans";
            
            $this->is_saved = FALSE;
            
            if (in_array($method, get_class_methods($this))) {
                $this->$method($value);
            } else {
                //Set Field as null if '' and its default is null
                if ($value == '' && in_array($name, $this->null_fields)) {
                    $this->fields[$name] = NULL;
                } elseif (in_array($name, $this->array_fields) && !is_array($value)) {
                    $value_array = $this->sqlArraytoPHP($value);
                    $this->fields[$name] = ($value_array) ?: [];
                } else {
                    $this->fields[$name] = $value;
                }
            }
        }
        
        return $this->is_saved;
    
    }

    /**
     * Returns a php type array from a SQL {1,2,3} array
     *
     * @param [type] $value
     * @return void
     */
    protected function sqlArrayToPHP($value) {
        if (is_array($value)) {
            return $value;
        }
        return explode(',',trim($value,'{} \t\n\r\0\x0B'));
    }

    protected function phpArrayToSql($value) {
        return "'{".implode(',',array_map([$this,"sqlArrayQuote"],$value))."}'";
    }

    /**
     * Last SQL
     *
     * @return string
     */
    public function lastSql() {
        return $this->last_sql;
    
    }

    /**
     * dbload
     *
     * Returns an object record from a db record.
     *
     * @param mixed $fields
     * @return static
     */
    public static function dbLoad($fields) {
        $obj = static::getNewSelf();
        $obj->setFields($fields);
        
        return $obj;
    
    }

    /**
     * Get
     *
     * Static function used to return an object for an existing row.
     * Returns null if the row can not be found.
     * Id can be an id or an array of ids (if the table has multiple primary keys).
     *
     * @param int|null $id
     *
     * @return static
     *
     */
    public static function get($id = NULL) {
        /** @var Table $obj */
        $obj = static::getNewSelf();
        
        if (is_null($id)) {
            return $obj;
        }
        
        if ($obj->load($id)) {
            return $obj;
        } else {
            $class = get_called_class();
            error_log("Create $class error");
        }
        
        return false;
    
    }

    /**
     * Load
     *
     * Internal function used to fill the fields array with the table data for the supplied id or array of ids (if the
     * table has multiple primary keys).
     *
     * @param mixed $id
     *
     * @return boolean
     */
    protected function load($id) {
        $sql = "SELECT * FROM {$this->table_prefix}{$this->table_name} WHERE {$this->pkeyWhereQuery($id)}";
        $object_fields = $this->db->getrow($sql);
        if ($object_fields) {
            $this->setFields($object_fields);
            $this->is_saved = TRUE;
            return TRUE;
        }
        
        return FALSE;
    
    }

    /**
     * Pkey Where Query
     *
     * Returns the WHERE clause sql query for the primary key(s)
     *
     * @param array $fields
     *
     * @return string
     */
    protected function pkeyWhereQuery($fields) {
        $queryArray = [];
        
        if (is_array($this->pkey)) {
            foreach ($this->pkey as $pkey) {
                $queryArray[] = "$pkey = {$this->db->qstr($fields[$pkey])}";
            }
        } else {
            $val = is_array($fields) ? $fields[$this->pkey] : $fields;
            
            return "$this->pkey = {$this->db->qstr($val)}";
        }
        
        return implode(' AND ', $queryArray);
    
    }

    /**
     * Get By Field
     *
     * Static function used to return an object with fields set from a row of data.
     * Returns null if the row can not be found.
     *
     * Field should be a string column name
     * Val should be a valid postgre data type
     *
     * @param string $field
     * @param mixed $val
     * @param bool $show_sql
     *
     * @return static
     */
    public static function getByField($field, $val = NULL, $show_sql = FALSE) {
        /** @var Table $obj */
        $obj = static::getNewSelf();
        
        if (is_null($val)) {
            return $obj;
        }
        
        if ($obj->loadByField($field, $val, $show_sql)) {
            return $obj;
        } else {
            return NULL;
        }
    
    }

    /**
     * Load By Field
     *
     * Internal function used to fill the fields array of an object
     * with the table data for the supplied id or array of ids (if the table has multiple primary keys).
     *
     * @param mixed $field
     * @param mixed $val
     * @param bool $show_sql
     *
     * @return boolean
     */
    public function loadByField($field, $val, $show_sql = FALSE) {
        if (!array_key_exists($field, $this->fields)) {
            return FALSE;
        }
        
        $val = $this->db->qstr($val);
        $sql = "SELECT * FROM {$this->table_prefix}{$this->table_name} WHERE $field = $val LIMIT 1";
        if ($show_sql) {
            echo $sql . "\n";
        }
        $db_fields = $this->db->getrow($sql);
        if ($db_fields) {
            $this->setFields($db_fields);
            return TRUE;
        } else {
            return FALSE;
        }
    
    }

    /**
     * Get All By Field
     *
     * Static function used to return an object for an existing row .
     * Returns null if the row can not be found.
     *
     * Field and val should be
     *
     * @param mixed $field
     * @param mixed $val
     * @param array $sort array('field', 'asc|desc')
     * @param int $limit
     * @param bool $show_sql
     *
     * @return array|false
     */
    public static function getAllByField($field, $val = NULL, array $sort = [], $limit = 0, $show_sql = FALSE) {
        /** @var Table $obj */
        $obj = static::getNewSelf();
        
        if (is_null($val)) {
            return NULL;
        }
        
        // Returns result set array
        return $obj->loadAllByField($field, $val, $sort, $limit, $show_sql);
    
    }

    /**
     * Load All By Field
     *
     * Internal function used to fill the fields array with the table data for the supplied id or array of ids (if the
     * table has multiple primary keys).
     *
     * @param mixed $field
     * @param mixed $val
     * @param array $sort array('field', 'asc|desc'
     * @param int $limit
     * @param bool $show_sql
     *
     * @return array|false
     */
    protected function loadAllByField($field, $val, $sort, $limit, $show_sql = FALSE) {
        $sql = "SELECT * FROM {$this->table_prefix}{$this->table_name}";
        if (trim($field) !== '') {
            if (!array_key_exists($field, $this->fields)) {
                return FALSE;
            }
            if (!is_numeric($val)) {
                $val = $this->db->qstr($val);
            }
            $sql .= " WHERE $field = $val";
        }
        
        if (count($sort) > 0) {
            list($sortCol, $sortOrd) = $sort;
            $sql .= " ORDER BY $sortCol $sortOrd ";
        }
        
        $limit = intval($limit);
        if ($limit > 0) {
            $sql .= " LIMIT $limit ";
        }
        if ($show_sql) {
            echo $sql;
        }
        return $this->db->GetAll($sql);
    
    }

    public static function pkName() {
        $Class = get_called_class();
        $RC = new \ReflectionClass($Class);
        $Self = (object) $RC->getDefaultProperties();
        return $Self->pkey;
    
    }

    /**
     * getNewSelf
     * 
     * @return static
     */
    public static function getNewSelf() {
        return new static();
    
    }

    public static function getStatic() {
        return new static();
    }

    /**
     * Is Created
     * Returns true if record was created
     * 
     * @return boolean
     */
    public function isCreated() {
        if ($this->insert_id) {
            return TRUE;
        } else {
            return FALSE;
        }
    
    }

    /**
     * Get Primary Key returns the primary key for a Table object
     * 
     * @return int|null
     */
    public function getPk() {
        return isset($this->fields[$this->pkey]) ? $this->fields[$this->pkey] : NULL;
    
    }

    /**
     * Updates the database record with current internal data.
     *
     * @param boolean $show_sql Whether or not to show the SQL
     */
    function save($show_sql = FALSE) {
        $columns = [];
        $object_fields = $this->getFields();
        
        foreach ($object_fields as $name=>$value) {
            
            //don't update the primary key or excluded fields
            if ($name == $this->pkey || in_array($name, $this->no_save) || is_array($this->pkey) && in_array($name, $this->pkey)) {
                continue;
            }
            
            //Nulls and Booleans can not be quoted in sql
            if (in_array($name, $this->array_fields)) {
                $columns[] = "$name = ".$this->phpArrayToSql($value);
            } else {
               if (is_null($value)) {
                    $columns[] = "$name=NULL";
                } else {
                    if (!is_numeric($value) && strlen($value) > 0) {
                        $value = trim($value);
                    }
                    $columns[] = "$name={$this->db->qstr($value)}";
                }
            }
        }
        
        $sql = "UPDATE {$this->table_prefix}{$this->table_name}
            SET " . implode(', ', $columns) . "
            WHERE {$this->pkeyWhereQuery($object_fields)}";
        $this->db->execute($sql);
        $this->last_sql = $sql;
        
        if ($this->db->ErrorMsg() != '') {
            $object_fields = $this->getFields();
            $this->error_msg = $this->db->ErrorMsg();
            $additional = ['sql'=>$sql, 'error_message'=>$this->error_msg] + (array) $object_fields;
            error_log("Table save error $sql \n {$this->error_msg}");
        }
        
        if ($show_sql) {
            echo "\n" . $this->error_msg . "\n" . $sql . "\n";
        }
        
        $this->checkSaved();
    
    }

    /**
     * check Saved
     *
     * @return boolean
     */
    protected function checkSaved() {
        $id = [];
        $diff = [];
        
        if (is_array($this->pkey)) {
            foreach ($this->pkey as $key) {
                $id[$key] = $this->getField($key);
            }
        } else {
            $id[$this->pkey] = $this->getField($this->pkey);
        }
        
        $sql = "SELECT * FROM {$this->table_prefix}{$this->table_name} WHERE {$this->pkeyWhereQuery($id)}";
        $dbFields = $this->db->getrow($sql);
        
        // for some reason, floats didn't equal, and were infinitessimally different.
        foreach ($this->getFields() as $field=>$value) {
            if (in_array($field, $this->no_save)) {
                continue;
            }
            
            if (is_int($value)) {
                if (intval($dbFields[$field]) != intval($value)) {
                    $diff[] = ['field'=>$field, 'value'=>$value, 'db_value'=>$dbFields[$field], 'type'=>'integer'];
                }
            } elseif (is_float($value)) {
                if (number_format($dbFields[$field], 2) != number_format($value, 2)) {
                    $diff[] = ['field'=>$field, 'value'=>$value, 'db_value'=>$dbFields[$field], 'type'=>'float'];
                }
            } elseif (in_array($field, $this->array_fields)) {
                if ($value != $this->sqlArrayToPHP($dbFields[$field])) {
                    print_r($dbFields[$field]);
                    echo "\n<br>$value";
                    $diff[] = ['field'=>$field, 'value'=>$value, 'db_value'=>$dbFields[$field], 'type'=>'array'];
                }
            } else {
                if (trim($dbFields[$field]) != trim($value)) {
                    $diff[] = ['field'=>$field, 
                        'value'=>'"' . $value . '"', 
                        'db_value'=>'"' . $dbFields[$field] . '"', 
                        'type'=>'other'];
                }
            }
        }
        
        if (count($diff) == 0) {
            $this->is_saved = TRUE;
        } else {
            $this->is_saved = FALSE;
            $this->problem_fields = $diff;
        }
    
    }

    /**
     * Error Message
     *
     * @return string
     */
    public function errorMsg() {
        return $this->error_msg;
    
    }

    /**
     * Is Saved
     *
     * @return boolean
     */
    public function isSaved() {
        return $this->is_saved;
    
    }

    /**
     * Problem Fields
     *
     * @return array
     */
    public function problemFields() {
        return $this->problem_fields;
    
    }

    /**
     * Returns the problem fields in a readable format
     */
    public function problemFieldsText() {
        $pft = [];
        foreach ($this->problem_fields as $p) {
            $pft[] = "Field: {$p['field']}, Type: {$p['type']}, Value: {$p['value']}, DB Value: {$p['value']}";
        }
        return $pft;
    
    }

    public function sqlOut($sql) {
        echo "<pre>$sql</pre>";
    
    }

    public function affectedRows() {
        return $this->db->affected_rows();
    
    }

    /**
     *
     * @return mixed
     */
    public function getTable() {
        return $this->table_prefix . $this->table_name;
    
    }

}
