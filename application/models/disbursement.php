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
		$this -> hasColumn('GD_Batch', 'varchar', 20);
		$this -> hasColumn('ID_Batch', 'varchar', 20);
		$this -> hasColumn('Timestamp', 'varchar', 32);

	}

	public function setUp() {
		$this -> setTableName('disbursement');
		$this -> hasOne('FBG as FBG_Object', array('local' => 'FBG', 'foreign' => 'id'));
		$this -> hasOne('Farm_Input as Farm_Input_Object', array('local' => 'Farm_Input', 'foreign' => 'id'));
	}

	public function getTotalDisbursements() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Disbursements") -> from("Disbursement");
		$total = $query -> execute();
		return $total[0]['Total_Disbursements'];
	}

	public function getPagedDisbursements($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursement") -> offset($offset) -> limit($items);
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

}