<?php
class Disbursement extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('FBG', 'varchar', 10);
		$this -> hasColumn('Invoice_Number', 'varchar', 20);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Farm_Input', 'varchar', 10);
		$this -> hasColumn('Quantity', 'varchar', 20);
		$this -> hasColumn('Total_Value', 'varchar', 20);
		$this -> hasColumn('Season', 'varchar', 20);
		$this -> hasColumn('ID_Batch', 'varchar', 20);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Agent', 'varchar', 10);
		$this -> hasColumn('Batch_Status', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('disbursement');
		$this -> hasOne('FBG as FBG_Object', array('local' => 'FBG', 'foreign' => 'id'));
		$this -> hasOne('Agent as Agent_Object', array('local' => 'Agent', 'foreign' => 'id'));
		$this -> hasOne('Farm_Input as Farm_Input_Object', array('local' => 'Farm_Input', 'foreign' => 'id'));
	}

	public function getTotalDisbursements($batch) {
		$query = Doctrine_Query::create() -> select("count(distinct Invoice_Number) as Total_Disbursements") -> from("Disbursement") -> where("ID_Batch = '$batch'");
		$total = $query -> execute();
		return $total[0]['Total_Disbursements'];
	}

	public function getPagedDisbursements($batch, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursement") -> where("ID_Batch = '$batch'") -> offset($offset) -> limit($items) -> orderBy("id Desc") -> groupBy("Invoice_Number");
		$disbursements = $query -> execute(array());
		return $disbursements;
	}

	public function getDisbursement($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursement") -> where("id = '$id'");
		$disbursement = $query -> execute();
		return $disbursement[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursement");
		$disbursement = $query -> execute();
		return $disbursement;
	}

	public function getBatchDisbursements($batch_id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursement") -> where("ID_Batch = '$batch_id'");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public function getInvoiceDisbursements($invoice) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursement") -> where("Invoice_Number = '$invoice'");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public function getInvoiceInputs($invoice) {
		$query = Doctrine_Query::create() -> select("Farm_Input") -> from("Disbursement") -> where("Invoice_Number = '$invoice'")->groupBy("Farm_Input");
		$inputs = $query -> execute();
		return $inputs;
	}

	public function getFBGDisbursements($fbg) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursement") -> where("fbg = '$fbg'");
		$disbursements = $query -> execute();
		return $disbursements;
	}

}
