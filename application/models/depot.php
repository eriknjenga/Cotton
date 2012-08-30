<?php
class Depot extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Depot_Code', 'varchar', 20);
		$this -> hasColumn('Depot_Name', 'varchar', 100);
		$this -> hasColumn('Buyer', 'varchar', 10);
		$this -> hasColumn('Village', 'varchar', 10);
		$this -> hasColumn('Capacity', 'varchar', 20);
		$this -> hasColumn('Distance', 'varchar', 10);
		$this -> hasColumn('Deleted', 'varchar', 1);
		$this -> hasColumn('Purchase_Route', 'varchar', 10);
		$this -> hasColumn('Cash_Disbursement_Route', 'varchar', 10);
		$this -> hasColumn('Collection_Route', 'varchar', 10);
		$this -> hasColumn('Audit_Route', 'varchar', 10);
		$this -> hasColumn('FBG', 'varchar', 1);
		$this -> hasColumn('Acre_Yield', 'varchar', 10);
		$this -> hasColumn('Acreage', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('depot');
		$this -> hasOne('Buyer as Buyer_Object', array('local' => 'Buyer', 'foreign' => 'id'));
		$this -> hasOne('Village as Village_Object', array('local' => 'Village', 'foreign' => 'id'));
		$this -> hasOne('Route as Purchase_Route_Object', array('local' => 'Purchase_Route', 'foreign' => 'id'));
		$this -> hasOne('Cash_Route as Cash_Disbursement_Route_Object', array('local' => 'Cash_Disbursement_Route', 'foreign' => 'id'));
		$this -> hasOne('Collection_Route as Collection_Route_Object', array('local' => 'Collection_Route', 'foreign' => 'id'));
		$this -> hasOne('Audit_Route as Audit_Route_Object', array('local' => 'Audit_Route', 'foreign' => 'id'));
	}

	public function getTotalDepots() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Depots") -> from("Depot") -> where("Deleted = '0'");
		$total = $query -> execute();
		return $total[0]['Total_Depots'];
	}

	public function getPagedDepots($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Depot") -> where("Deleted != '1'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$depots = $query -> execute(array());
		return $depots;
	}

	public function getTotalSearchedDepots($search_value) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Depots") -> from("depot") -> where("(Depot_Code like '%$search_value%' or Depot_Name like '%$search_value%') and Deleted != '1'");
		$total = $query -> execute();
		return $total[0]['Total_Depots'];
	}

	public function getPagedSearchedDepots($search_value, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("depot") -> where("(Depot_Code like '%$search_value%' or Depot_Name like '%$search_value%') and Deleted != '1'") -> offset($offset) -> limit($items);

		$depots = $query -> execute(array());
		return $depots;
	}

	public function getDepot($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Depot") -> where("id = '$id'");
		$depot = $query -> execute();
		return $depot[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Depot") -> where("Deleted != '1'")->orderBy("Depot_Name asc");
		$depots = $query -> execute();
		return $depots;
	}

}
