<?php
class Region_Input_Issue extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Delivery_Note_Number', 'varchar', 100);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Farm_Input', 'varchar', 10);
		$this -> hasColumn('Quantity', 'varchar', 20);
		$this -> hasColumn('Total_Value', 'varchar', 20);
		$this -> hasColumn('Season', 'varchar', 20);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Agent', 'varchar', 10);
		$this -> hasColumn('Region', 'varchar', 10);

	}

	public function setUp() {
		$this -> setTableName('region_input_issue');
		$this -> hasOne('Agent as Agent_Object', array('local' => 'Agent', 'foreign' => 'id'));
		$this -> hasOne('Region as Region_Object', array('local' => 'Region', 'foreign' => 'id'));
		$this -> hasOne('Farm_Input as Farm_Input_Object', array('local' => 'Farm_Input', 'foreign' => 'id'));
	}

	public function getTotalIssues() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Issues") -> from("Region_Input_Issue");
		$total = $query -> execute();
		return $total[0]['Total_Issues'];
	}

	public function getPagedIssues($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Region_Input_Issue") -> offset($offset) -> limit($items);
		$issues = $query -> execute(array());
		return $issues;
	}

	public function getIssue($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Region_Input_Issue") -> where("id = '$id'");
		$issue = $query -> execute();
		return $issue[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Region_Input_Issue");
		$issues = $query -> execute();
		return $issues;
	}

}
