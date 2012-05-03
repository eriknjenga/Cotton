<?php
class Disbursement_Details extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Invoice', 'varchar', 30);
		$this -> hasColumn('Farmer', 'text');
		$this -> hasColumn('Farm_Input', 'varchar', 10);
		$this -> hasColumn('Quantity', 'varchar', 10);
		$this -> hasColumn('Total_Value', 'varchar', 20);
		$this -> hasColumn('Batch', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('disbursement_details');
	} 

}
