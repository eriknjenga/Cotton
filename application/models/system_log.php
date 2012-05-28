<?php
class System_Log extends Doctrine_Record {

	public function setTableDefinition() {
		/*
		 * Log Types:
		 * 1 - New Record
		 * 2 - Edited Record
		 * 3 - Deleted Record
		 * 4 - Downloaded Record(s)
		 */
		$this -> hasColumn('Log_Type', 'varchar', 10);
		$this -> hasColumn('Log_Message', 'text');
		$this -> hasColumn('User', 'varchar', 10);
		$this -> hasColumn('Timestamp', 'varchar', 32);
	}

	public function setUp() {
		$this -> setTableName('system_log');
		$this -> hasOne('User as Creator', array('local' => 'User', 'foreign' => 'id'));
	}

	public function getTotalLogs($type) {
		if ($type > 0) {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Logs") -> from("System_Log") -> where("Log_Type = '$type'");
		} else {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Logs") -> from("System_Log");
		}
		$total = $query -> execute();
		return $total[0]['Total_Logs'];
	}

	public function getPagedLogs($type, $offset, $items) {
		if ($type > 0) {
			$query = Doctrine_Query::create() -> select("*") -> from("System_Log") -> where("Log_Type = '$type'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		} else {
			$query = Doctrine_Query::create() -> select("*") -> from("System_Log") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		}

		$logs = $query -> execute(array());
		return $logs;
	}

	public function getLog($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("System_Log") -> where("id = '$id'");
		$log = $query -> execute();
		return $log[0];
	}

}
