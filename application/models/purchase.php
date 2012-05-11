<?php
class Purchase extends Doctrine_Record {
	public function setTableDefinition() {
		/*
		 * Posted: 0 - Not Posted, 1 - Posted
		 */
		$this -> hasColumn('FBG', 'varchar', 10);
		$this -> hasColumn('DPN', 'varchar', 20);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Depot', 'varchar', 10);
		$this -> hasColumn('Quantity', 'varchar', 20);
		$this -> hasColumn('Unit_Price', 'varchar', 20);
		$this -> hasColumn('Gross_Value', 'varchar', 20);
		$this -> hasColumn('Net_Value', 'varchar', 20);
		$this -> hasColumn('Season', 'varchar', 20);
		$this -> hasColumn('Loan_Recovery', 'varchar', 20);
		$this -> hasColumn('Farmer_Reg_Fee', 'varchar', 20);
		$this -> hasColumn('Other_Recoveries', 'varchar', 20);
		$this -> hasColumn('Buyer', 'varchar', 10);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Batch', 'varchar', 20);
		$this -> hasColumn('Batch_Status', 'varchar', 2);

	}

	public function setUp() {
		$this -> setTableName('purchase');
		$this -> hasOne('FBG as FBG_Object', array('local' => 'FBG', 'foreign' => 'id'));
		$this -> hasOne('Buyer as Buyer_Object', array('local' => 'Buyer', 'foreign' => 'id'));
		$this -> hasOne('Depot as Depot_Object', array('local' => 'Depot', 'foreign' => 'id'));
	}

	public function getTotalPurchases($batch) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Purchases") -> from("Purchase")->where("Batch = '$batch'");
		$total = $query -> execute();
		return $total[0]['Total_Purchases'];
	}

	public function getPagedPurchases($batch,$offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Purchase") ->where("Batch = '$batch'")-> offset($offset) -> limit($items) -> orderBy("id Desc");
		$purchases = $query -> execute(array());
		return $purchases;
	}

	public function getPurchase($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Purchase") -> where("id = '$id'");
		$purchase = $query -> execute();
		return $purchase[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Purchase");
		$purchase = $query -> execute();
		return $purchase;
	}

	public function getFBGPurchases($fbg) {
		$query = Doctrine_Query::create() -> select("*") -> from("Purchase") -> where("fbg = '$fbg'");
		$purchases = $query -> execute();
		return $purchases;
	}

	public function getBatchPurchases($batch_id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Purchase") -> where("Batch = '$batch_id'");
		$purchases = $query -> execute();
		return $purchases;
	}

}
