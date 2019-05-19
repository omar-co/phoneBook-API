<?php


namespace src\Services;

use \PDO;
use src\Config\Db as ConfigDb;
class Db
{
    # @object, The PDO object
    private $pdo;

    # @object, PDO statement object
    private $sQuery;

    # @bool ,  Connected to the database
    private $bConnected = false;

    # @array, The parameters of the SQL query
    private $parameters;

    /**
     *   Default Constructor
     *
     *	1. Instantiate Log class.
     *	2. Connect to database.
     *	3. Creates the parameter array.
     */
    public function __construct()
    {
        $this->Connect();
        $this->parameters = array();
    }

    /**
     *	This method makes connection to the database.
     *
     */
    private function Connect()
    {
        $config = ConfigDb::dbConfig();
        $dsn = 'mysql:dbname='.$config['database'].';host='.$config['host'];
        try
        {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $this->bConnected = true;
        }
        catch (PDOException $e)
        {
            echo $this->ExceptionLog($e->getMessage());
            die();
        }
    }


    /**
     *	Every method which needs to execute a SQL query uses this method.
     *
     */
    private function Init($query,$parameters = "")
    {
        if(!$this->bConnected) { $this->Connect(); }
        try {
            # Prepare query
            $this->sQuery = $this->pdo->prepare($query);

            # Add parameters to the parameter array
            $this->bindMore($parameters);

            # Bind parameters
            if(!empty($this->parameters)) {
                foreach($this->parameters as $param)
                {
                    $parameters = explode("\x7F",$param);
                    $this->sQuery->bindParam($parameters[0],$parameters[1]);
                }
            }

            $this->success = $this->sQuery->execute();
        }
        catch(PDOException $e)
        {
            $this->ExceptionLog($e->getMessage(), $query );
        }
        $this->parameters = array();
    }

    /**
     *	@void
     *
     *	Add the parameter to the parameter array
     */
    public function bind($para, $value)
    {
        $this->parameters[count($this->parameters)] = ':' . $para . "\x7F" . utf8_encode($value);
    }
    /**
     * Add more parameters to the parameter array
     *
     *	@void
     *	@param array $parray
     */
    public function bindMore($parray)
    {
        if(empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach($columns as $i => &$column)	{
                $this->bind($column, $parray[$column]);
            }
        }
    }
    /**
     *  If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
     *	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     *
     *  @param  string $query
     *	@param  array  $params
     *	@param  int    $fetchmode
     *	@return mixed
     */
    public function query($query,$params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $query = trim($query);
        $this->Init($query,$params);
        $rawStatement = explode(" ", $query);

        $statement = strtolower($rawStatement[0]);

        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchmode);
        }
        elseif ( $statement === 'insert' ||  $statement === 'update' || $statement === 'delete' ) {
            return $this->pdo->lastInsertId();
        }
        else {
            return NULL;
        }
    }

    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     *	Returns an array which represents a column from the result set
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return array
     */
    public function column($query,$params = null)
    {
        $this->Init($query,$params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);
        $column = null;
        foreach($Columns as $cells) {
            $column[] = $cells[0];
        }
        return $column;

    }
    /**
     *	Returns an array which represents a row from the result set
     *
     *	@param  string $query
     *	@param  array  $params
     *  @param  int    $fetchmode
     *	@return array
     */
    public function row($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
    {
        $this->Init($query,$params);
        return $this->sQuery->fetch($fetchmode);
    }
    /**
     *	Returns the value of one single field/column
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return string
     */
    public function single($query,$params = null)
    {
        $this->Init($query,$params);
        return $this->sQuery->fetchColumn();
    }
    /**
     * returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return string
     */
    private function ExceptionLog($message , $sql = "")
    {
        $exception  = 'Unhandled Exception. <br />';
        $exception .= $message;
        if(!empty($sql)) {
            $message .= "\r\nRaw SQL : "  . $sql;
        }
        throw new Exception($message);
    }
}
?>