<?php
/**
* @package mysql-database
*/

/**
* MySQL Database Connection Class
*
* Provides basic database connectivity, query, and result set functionality.
* Allows the createion of multiple database objects for threaded applications,
* as opposed to singleton functions, which restrict the developer to one object instance.
*
* Methods:
*
* 1. open
* 2. close
* 3. query
* 4. count_rows
* 5. free_result
*
* Properties:
*
* 1. dbUser //database username
* 2. dbPass //database password
* 3. dbHost //database host
* 4. dbName //database name
* 5. dbError //error output
*
* @package mysql-database
* @author Isaac N. Hopper
* @copyright (c) 2011 - Isaac N. Hopper
* @version 1.0
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License v3
*/

class MySqlDB 
{

	//Variables
	public $dbUser, $dbPass, $dbHost, $dbName, $sql, $result, $err_msg;
	private $db;

	//Variables for paged display
    var $num_rows;            // NUmber of records in DB  
    var $rec_per_page;        // Records per page to display
    var $tot_pages;            // Total Pages
    var $html_code;            // Html Code for display
	public $pagelinks;			//HTML code for displaying the pagelinks
	public $start;			//Starting position for pagelinks
	var $pagenum;			// Current page number


	//Cunstructor
	function __construct($db_user, $db_pass, $db_host, $database) {
		$this->dbUser = $db_user;
		$this->dbPass = $db_pass;
		$this->dbHost = $db_host;
		$this->dbName = $database;
	}


	//Open a database connection
	public function open()
	{
		$db=mysql_connect($this->dbHost, $this->dbUser, $this->dbPass) or die ("cannot connect");
		mysql_select_db($this->dbName) or die ("cannot access database");
		//Debug
		//echo "Connected!";
		return $db;
	
	}
	
	//Run a database query
	public function query($sql)
	{
		$result = mysql_query($sql) or die ("Query Failed: $sql //" . mysql_error());
		return $result;
	}
	

	//Paged results
	public function page_results($sql, $rec_per_page, $pagenum)
	{
		//Run the query
		$result = mysql_query($sql) or die("Couldn't connected mysql: $sql");

		//Count the results
		$num_rows = mysql_num_rows($result);

		//Assign variables
        $this->num_rows = $num_rows;
        $this->rec_per_page = $rec_per_page;
        $this->tot_pages = ceil($this->num_rows / $this->rec_per_page);
        
		//Free memory for next query
		mysql_free_result($result);

		//Re-run query with a limit
		$this->start = ($rec_per_page * $pagenum) - $rec_per_page; //i.e. (3*20)-20=40 
		$sql = $sql . " limit $this->start,$rec_per_page";
		$result = mysql_query($sql);

		//Generate pagelinks (displayed by external call)
		$pagename = basename($_SERVER[PHP_SELF]);//get current filename

		$this->pagelinks = "<div id='pagelinks'>";
		if ($this->tot_pages == 1)
		{
			$this->pagelinks .= "<span style='float: left; font-size: 10pt;'>Page: &nbsp;</span><li>1<li>";//Change to <li> after tests
		} else {
			for ($i=1; $i <= $this->tot_pages; $i++)
				{
					$this->pagelinks .= "<li> | <a href='$pagename?page=$i'>" . $i . "</a></li>";
				}//end for
		}//end else
		$this->pagelinks .= "</div>";

		return $result;
	}

	//Close a database connection
	public function close($db)
	{
		mysql_close($db);
	
	}
	
	//Count the returned rows
	public function count_rows($result)
	{
		//Count the rows in $results
		$num_rows = mysql_num_rows($result);	
		return $num_rows;
	}

	//Free result set memory - in prep for new query
	public function free_result($result)
	{
		mysql_free_result($result);
	}


	//Error handler
	public function error($db)
	{
		$err_no = mysql_errno($db);
		$err_msg = mysql_error($db);
		return $err_msg;
	}

}

?>