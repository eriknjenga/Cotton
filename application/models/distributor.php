<?php
class Distributor extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Distributor_Code', 'varchar', 20);
		$this -> hasColumn('First_Name', 'varchar', 50);
		$this -> hasColumn('Surname', 'varchar', 50);
		$this -> hasColumn('National_Id', 'varchar', 50);
		$this -> hasColumn('Area', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('distributor');
		$this -> hasOne('Area as Area_Object', array('local' => 'Area', 'foreign' => 'id'));
	}

	public function getTotalDistributors() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Distributors") -> from("distributor");
		$total = $query -> execute();
		return $total[0]['Total_Distributors'];
	}

	public function getPagedDistributors($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Distributor") -> offset($offset) -> limit($items);
		$distributors = $query -> execute(array());
		return $distributors;
	}

	public function getDistributor($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Distributor") -> where("id = '$id'");
		$distributor = $query -> execute();
		return $distributor[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Distributor");
		$distributors = $query -> execute();
		return $distributors;
	}

}
