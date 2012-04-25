<?php
class Transaction_Batch extends Doctrine_Record {
	public function setTableDefinition() {
		/*
		 * Status: 0 - Active, 1 - Closed
		 * Transaction Types: 0 - Input Disbursements, 1 - Purchases
		 */
		$this -> hasColumn('Transaction_Type', 'varchar', 10);
		$this -> hasColumn('User', 'varchar', 10);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Status', 'varchar', 5);
		$this -> hasColumn('Validated_By', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('transaction_batch');
		$this -> hasOne('User as User_Object', array('local' => 'User', 'foreign' => 'id'));
		$this -> hasMany('Disbursement as Disbursements', array('local' => 'id', 'foreign' => 'ID_Batch'));
		$this -> hasMany('Purchase as Purchases', array('local' => 'id', 'foreign' => 'Batch'));
	}

	public function getTotalBatches($user) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Batches") -> from("Transaction_Batch") -> where("User = '$user'");
		$total = $query -> execute();
		return $total[0]['Total_Batches'];
	}

	public function getPagedBatches($offset, $items, $user) {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("User = '$user'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$batches = $query -> execute(array());
		return $batches;
	}

	public function getBatch($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("id = '$id'");
		$batch = $query -> execute();
		return $batch[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch");
		$batches = $query -> execute();
		return $batches;
	}

	public function getActiveUserBatches($user,$type) {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch")->where("User = '$user' and Status = '0' and Transaction_Type = '$type'");
		$batches = $query -> execute();
		return $batches;
	}

}
