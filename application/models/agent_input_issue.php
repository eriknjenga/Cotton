<?php
class Agent_Input_Issue extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Delivery_Note_Number', 'varchar', 100);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Farm_Input', 'varchar', 10);
		$this -> hasColumn('Quantity', 'varchar', 20);
		$this -> hasColumn('Total_Value', 'varchar', 20);
		$this -> hasColumn('Season', 'varchar', 20);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Agent', 'varchar', 10);
		$this -> hasColumn('Batch', 'varchar', 10);
		$this -> hasColumn('Batch_Status', 'varchar', 5);

	}

	public function setUp() {
		$this -> setTableName('agent_input_issue');
		$this -> hasOne('Agent as Agent_Object', array('local' => 'Agent', 'foreign' => 'id'));
		$this -> hasOne('Farm_Input as Farm_Input_Object', array('local' => 'Farm_Input', 'foreign' => 'id'));
	}

	public function getTotalIssues($batch) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Issues") -> from("Agent_Input_Issue") -> where("Batch = '$batch'");
		$total = $query -> execute();
		return $total[0]['Total_Issues'];
	}

	public function getPagedIssues($batch, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Agent_Input_Issue") -> where("Batch = '$batch'") -> offset($offset) -> limit($items);
		$issues = $query -> execute(array());
		return $issues;
	}

	public function getIssue($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Agent_Input_Issue") -> where("id = '$id'");
		$issue = $query -> execute();
		return $issue[0];
	}

	public function getBatchDisbursements($batch_id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Agent_Input_Issue") -> where("Batch = '$batch_id'");
		$issues = $query -> execute(array());
		return $issues;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Agent_Input_Issue");
		$issues = $query -> execute();
		return $issues;
	}

}
