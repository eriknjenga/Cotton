<?php
class Production_Data extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Ginnery', 'varchar', 2);
		$this -> hasColumn('Date', 'varchar', 10);
		$this -> hasColumn('Time', 'varchar', 10);
		$this -> hasColumn('Lot_Number', 'varchar', 30);
		$this -> hasColumn('Consecutive_Number', 'varchar', 10);
		$this -> hasColumn('Gross_Weight', 'varchar', 10); 
	}

	public function setUp() {
		$this -> setTableName('production_data');
	}

	public function getSearchedLotNumber($ticket) {
		$query = Doctrine_Query::create() -> select("*") -> from("Production_Date") -> where("Lot_Number = '$ticket'");
		$ticket = $query -> execute();
		return $ticket;
	}

}
