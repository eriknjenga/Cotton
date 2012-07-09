<?php
class Depot_Closure extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Depot', 'varchar', 10);
		$this -> hasColumn('Date_Closed', 'varchar', 15);
		$this -> hasColumn('Reason', 'text');
	}

	public function setUp() {
		$this -> setTableName('depot_closure');
	}

}
