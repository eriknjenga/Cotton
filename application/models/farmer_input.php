<?php
class Farmer_Input extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('FBG', 'varchar', 10);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Invoice_Number', 'varchar', 10);
		$this -> hasColumn('Farmer', 'text');
		$this -> hasColumn('Farm_Input', 'varchar', 10);
		$this -> hasColumn('Quantity', 'varchar', 20);
		$this -> hasColumn('Unit_Price', 'varchar', 20);
		$this -> hasColumn('Total_Value', 'varchar', 20);
		$this -> hasColumn('Batch_Id', 'varchar', 10);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Batch_Status', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('farmer_input');
	}

	public function getInvoiceDisbursements($invoice) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farmer_Input") -> where("Invoice_Number = '$invoice'");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public function getBatchDisbursements($batch) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farmer_Input") -> where("Batch_Id = '$batch'");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public function getDisbursement($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farmer_Input") -> where("id = '$id'");
		$farmer_inputs = $query -> execute();
		return $farmer_inputs[0];
	}

}
