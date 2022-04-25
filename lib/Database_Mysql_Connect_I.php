<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-13-2022
 ****************************************************************
 *  Database_Mysql_Connect_I.php
 *  Mysql connection and manipulation.
 *
 *************************************************************/

class Mysql_Connect_I
{
  private $mySQLConnection;
  private $mySQLresults;
  private $mySQLRowCount;
  private $myUsername;
  private $myDatabase;
  private $myHost;
  private $myPassword;
  private $myInsertID;



    private function SetUsername($username){
        $this->myUsername = $username;
    }

    private function SetPassword($password){
        $this->myPassword = $password;
    }

    private function SetDatabaseName($databaseName){
        $this->myDatabase = $databaseName;
    }

    private function SetHost($hostName){
        $this->myHost = $hostName;
    }

    private function SetRowCount($rowCount){
        if($rowCount == null){
            $this->mySQLRowCount = 0;
            return;
        }
        $this->mySQLRowCount = $rowCount;
    }

    private function SetLastInsertID($insertID){
        $this->myInsertID = $insertID;
    }

    // Main connection
    public function __construct($hostName, $databaseName, $username, $password)
    {
        $this->SetUsername($username);
        $this->SetPassword($password);
        $this->SetDatabaseName($databaseName);
        $this->SetHost($hostName);
        $this->openDatabase();

    }


    public function GetRowCount(){
        return $this->mySQLRowCount;
    }

    public function GetLastInsertId(){
        return $this->myInsertID;
    }



// OPEN DATABASE - openDatabase();
// Connects to the MySQL server and retrieves the database.
//
    private function openDatabase() {

        // Default to local host if a hostname is not provided
        if (!$this->myHost) {
            $this->SetHost("localhost") ;
        }

        // Opens connection to MySQL server
        $this->mySQLConnection = mysqli_connect($this->myHost, $this->myUsername, $this->myPassword);  //@mysqli_connect($this->myHost, $this->myUsername, $this->myPassword, $this->myDatabase);
        if(mysqli_connect_errno ()){
            die("<B>An error occurred while trying to connect to the MySQL server.</B> MySQL returned the following error information: " .mysqli_connect_errno (). mysqli_connect_error() .")");
        }

        $this->ChangeDatabase($this->myDatabase);


    }

    public function ChangeDatabase($databasename){
        if($databasename != $this->myDatabase){
            $this->SetDatabaseName($databasename);
        }

        if(!mysqli_select_db($this->mySQLConnection, $databasename)) {
            die("<B>Unable to locate the database.</B> Please double check <I>config.php</I> to make sure the <I>\$db_name</I> variable is set correctly.");
        }
    }


    public Function SelectQuery($select, $table, $where, $orderby = null)
    {
        $query = $this->buildquery($select, $table, $where, $orderby, 'SELECT');
        if($query == -1){
            die('Badly Formed Query in Database_Mysql_Connect_I.');
        }
        $this->mySQLresults = $this->mySQLConnection->query($query);
        if($this->mySQLresults == false){
            die('query error:' . $query);
        }
    }

    public function UpdateQuery($toUpdate, $table, $where){
        $query = $this->buildquery($toUpdate, $table, $where, '', 'UPDATE');
        if($query == -1){
            die('Badly Formed Query in Database_Mysql_Connect_I.');
        }
        $this->mySQLresults=$this->mySQLConnection->query($query);
        if($this->mySQLresults == false){
            die('query error.');
        }
        $this->SetRowCount($this->mySQLConnection->affected_rows);
        //Clear Results;
        $this->mySQLConnection->free();
    }

    /**
     * CommandQuery
     * Query takes ANY sql command.  and immidiately returns results.
     * User SPARINGLY as this CAN CAUSE EXPLOSIONS!!!!!
     * DOES NOT PROVIDE A ROW COUNT ONLY AN ARRAY!
     *
     * @return array
     */
    public function CommandQuery($query){
        $this->mySQLresults=$this->mySQLConnection->query($query);
        $this->SetRowCount($this->mySQLConnection->affected_rows);

        return $this->FetchQueryResult();
    }

    public function InsertQuery($insert, $table){
        $query = $this->buildquery($insert, $table, '','', 'INSERT');
        $this->mySQLresults=$this->mySQLConnection->query($query);
        if($this->mySQLresults == false){
            die('query error.');
        }
        $this->MySQLLastInsertID =
            $this->SetLastInsertID($this->mySQLConnection->insert_id);
        $this->SetRowCount($this->mySQLConnection->affected_rows);

        //Clear Results;
        $this->mySQLConnection->free();
    }


    public function DeleteQuery($where, $table){
        $query = $this->buildquery($where, $table, '','', 'DELETE');
        $this->mySQLresults=$this->mySQLConnection->query($query);
        if($this->mySQLresults == false){
            die('query error.');
        }
        $this->SetRowCount($this->mySQLConnection->affected_rows);
        //Clear Results;
        $this->mySQLConnection->free();
    }


    public function FreeFormQueryNoErrorchecking($query, $permissions){
        if($permissions == 1091){
            $this->mySQLresults=$this->mySQLConnection->query($query);
        }
    }


    public Function FetchQueryResult(){
        $results = array();
        $this->SetRowCount($this->mySQLresults->num_rows);
        if($this->GetRowCount() > 0) {
            if($this->GetRowCount() == 1) {
                $results[0] = $this->mySQLresults->fetch_array();
            }
            else{
                while( $row = $this->mySQLresults->fetch_array()){
                    $results[] = $row;
                }
            }
            //free results
            $this->mySQLresults->free();
            return $results;
        }
        //free results
        $this->mySQLresults->free();
        return -1;  // Something went horribly wrong.
    }

    public function SelectCount($table, $where){
        $this->SelectQuery("1 as 'cnt'", $table, $where, NULL);
        $count=$this->FetchQueryResult();
        if(is_array($count) != true){
            return 0;
        }
        return $count['cnt'];
    }



    private function buildquery($wants, $table, $where, $other, $type){
        switch($type){
            case 'SELECT':
                    if($wants == '' || $wants == NULL || $table == '' || $table == NULL){
                        return -1;
                    }
                    return $this->buildSelect ($wants, $table, $where, $other);
                    break;
            case 'UPDATE':
                if(!is_array($wants) || count($wants) == 0 || $wants == NULL || $table == '' || $table == NULL){
                    return -1;
                }
                return $this->buildUpdate ($wants, $table, $where);
            case 'INSERT':
                if(!is_array($wants) || count($wants) == 0 || $wants == NULL || $table == '' || $table == NULL){
                    return -1;
                }
                return $this->buildDelete($wants, $table);
            case 'DELETE':
                if($wants == "" || $wants == NULL || $table == '' || $table == NULL){
                    return -1;
                }
                return $this->buildInsert($wants, $table);
            default:
                return -1;
                break;

        }
    }

    private function buildSelect($select, $table, $where, $orderby){
         $query = 'Select '.$select.' from '.$table;

        if($where != '' || $where != NULL){
            // todo: redo the where to actually build it
            $query = $query. ' where '.$where;
        }
        if($orderby != '' || $orderby != NULL){
            // todo: redo the $orderby to actually build it
            $query = $query. ' '.$orderby;
        }
        return $query;
    }





    private function  buildUpdate($toUpdate, $table, $where){
        $Select = '';
        $countOfSelects = 0;
        foreach ($toUpdate as $key => $value){
            $Select .= " ".$key."=".$value;
            $countOfSelects++;
            if(count($toUpdate) !=  $countOfSelects){
                $Select .=",";
            }
        }
        $Query = "Update ". $table ." Set ". $Select;
        if($where !='' || $where != NULL){
            $Query .= " Where ".$where;
        }
        return $Query;
    }





    private function buildInsert($Toinsert, $table){
        $insertkey = '';
        $insertvalue = '';
        $countOfSelects = 0;
        foreach ($Toinsert as $key => $value){
            $insertkey .= " ".$key;
            $insertvalue .= " ".$value;
            $countOfSelects++;
            if(count($Toinsert) !=  $countOfSelects){
                $insertkey .=",";
                $insertvalue .=",";
            }
        }
        $Query = "Insert into". $table ." (".$insertkey.") Values(".$insertvalue.")";

        return $Query;
    }



    private function buildDelete($where, $table){
        $query = 'Delete From'.$table.' where '.$where;
        return $query;
    }

}