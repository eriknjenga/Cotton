<?php
class Agent extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Agent_Code', 'varchar', 20);
		$this -> hasColumn('First_Name', 'varchar', 50);
		$this -> hasColumn('Surname', 'varchar', 50);
		$this -> hasColumn('National_Id', 'varchar', 50);
		$this -> hasColumn('Deleted', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('agent');
	}

	public function getTotalAgents() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Agents") -> from("agent") -> where("Deleted = '0'");
		$total = $query -> execute();
		return $total[0]['Total_Agents'];
	}

	public function getPagedAgents($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("agent") -> where("Deleted = '0'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$agents = $query -> execute(array());
		return $agents;
	}

	public function getAgent($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("agent") -> where("id = '$id'");
		$agent = $query -> execute();
		return $agent[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Agent")-> where("Deleted = '0'");
		$agents = $query -> execute();
		return $agents;
	}

}
