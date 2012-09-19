<?php
class Transaction_Batch extends Doctrine_Record {
	public function setTableDefinition() {
		/*
		 * Status: 0 - Open, 1 - Closed, 2 - Posted
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
		$this -> hasOne('User as Validator_Object', array('local' => 'Validated_By', 'foreign' => 'id'));
		$this -> hasOne('Transaction_Type as Transaction_Type_Object', array('local' => 'Transaction_Type', 'foreign' => 'id'));
		$this -> hasMany('Disbursement as Disbursements', array('local' => 'id', 'foreign' => 'ID_Batch'));
		$this -> hasMany('Purchase as Purchases', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Agent_Input_Issue as Agent_Disbursements', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Buying_Center_Receipt as Buying_Center_Receipts', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Cash_Receipt as Cash_Receipts', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Cash_Disbursement as Cash_Disbursements', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Field_Cash_Disbursement as Field_Cash_Disbursements', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Region_Input_Issue as Region_Disbursements', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Mopping_Payment as Mopping_Payments', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Loan_Recovery_Receipt as Loan_Recovery_Cash_Receipts', array('local' => 'id', 'foreign' => 'Batch'));
		$this -> hasMany('Buying_Center_Summary as Buying_Center_Summaries', array('local' => 'id', 'foreign' => 'Batch'));
	}

	//For the data entry clerk
	public function getTotalBatches($user, $status = "") {
		if ($status != "N") {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Batches") -> from("Transaction_Batch") -> where("User = '$user' and Status = '$status'");
		} else {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Batches") -> from("Transaction_Batch") -> where("User = '$user'");
		}

		$total = $query -> execute();
		return $total[0]['Total_Batches'];
	}

	public function getPagedBatches($offset, $items, $user, $status = "") {
		if ($status != "N") {
			$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("User = '$user' and Status = '$status'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		} else {
			$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("User = '$user'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		}
		$batches = $query -> execute(array());
		return $batches;
	}

	//For the general supervisor. Get batches that are not open
	public function getTotalClosedBatches($status = "") {
		if ($status != "N") {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Batches") -> from("Transaction_Batch") -> where("Status = '$status'");
		} else {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Batches") -> from("Transaction_Batch");
		}
		$total = $query -> execute();
		return $total[0]['Total_Batches'];
	}

	public function getPagedClosedBatches($offset, $items, $status = "") {
		if ($status != "N") {
			$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("Status = '$status'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		} else {
			$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		}
		$batches = $query -> execute(array());
		return $batches;
	}

	//For the super administrator
	public function getTotalSystemBatches($status = "") {
		if ($status != "N") {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Batches") -> from("Transaction_Batch") -> where("Status = '$status'");
		} else {
			$query = Doctrine_Query::create() -> select("count(*) as Total_Batches") -> from("Transaction_Batch");
		}
		$total = $query -> execute();
		return $total[0]['Total_Batches'];
	}

	public function getPagedSystemBatches($offset, $items, $status = "") {
		if ($status != "N") {
			$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("Status = '$status'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		} else {
			$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		}

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

	public function getOpenUserBatches($user, $type) {
		$transaction_type = Transaction_Type::getTypeId($type);
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("User = '$user' and Status = '0' and Transaction_Type = '$transaction_type'");
		$batches = $query -> execute();
		return $batches;
	}

	public function getSearchedUserBatch($batch, $user) {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("Id = '$batch' and User='$user'");
		$batch = $query -> execute();
		return $batch;
	}

	public function getSearchedAdminBatch($batch) {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("Id = '$batch'");
		$batch = $query -> execute();
		return $batch;
	}

	public function getSearchedSupervisorBatch($batch) {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Batch") -> where("Id = '$batch'");
		$batch = $query -> execute();
		return $batch;
	}

}
