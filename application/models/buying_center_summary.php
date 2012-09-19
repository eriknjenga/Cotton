<?php
class Buying_Center_Summary extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Summary_Number', 'varchar', 10);
		$this -> hasColumn('Date', 'varchar', 10);
		$this -> hasColumn('Depot', 'varchar', 10);
		$this -> hasColumn('Opening_Bags', 'varchar', 10);
		$this -> hasColumn('Opening_Stocks', 'varchar', 10);
		$this -> hasColumn('Opening_Cash', 'varchar', 10);
		$this -> hasColumn('Bags_Received', 'varchar', 10);
		$this -> hasColumn('Cash_Received', 'varchar', 10);
		$this -> hasColumn('Start_Ppv', 'varchar', 10);
		$this -> hasColumn('End_Ppv', 'varchar', 10);
		$this -> hasColumn('Purchase_Quantity', 'varchar', 10);
		$this -> hasColumn('Purchase_Value', 'varchar', 10);
		$this -> hasColumn('Input_Deductions', 'varchar', 10);
		$this -> hasColumn('Cotton_Deliveries', 'varchar', 10);
		$this -> hasColumn('Delivery_Note', 'varchar', 10);
		$this -> hasColumn('Closing_Bags', 'varchar', 10);
		$this -> hasColumn('Closing_Stock', 'varchar', 10);
		$this -> hasColumn('Closing_Cash', 'varchar', 10);
		$this -> hasColumn('Prepared_By', 'varchar', 100);
		$this -> hasColumn('Batch', 'varchar', 10);
		$this -> hasColumn('Batch_Status', 'varchar', 5);
		$this -> hasColumn('Adjustment', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('buying_center_summary');
		$this -> hasOne('Depot as Depot_Object', array('local' => 'Depot', 'foreign' => 'id'));
		$this -> hasOne('Transaction_Batch as Batch_Object', array('local' => 'Batch', 'foreign' => 'id'));
	}

	public function getTotalSummaries($batch) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Summaries") -> from("Buying_Center_Summary") -> where("Batch = '$batch'");
		$total = $query -> execute();
		return $total[0]['Total_Summaries'];
	}

	public function getPagedSummaries($batch, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Buying_Center_Summary") -> where("Batch = '$batch'") -> offset($offset) -> limit($items);
		$summaries = $query -> execute(array());
		return $summaries;
	}

	public function getSummary($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Buying_Center_Summary") -> where("id = '$id'");
		$summary = $query -> execute();
		return $summary[0];
	}

	public function getBatchSummaries($batch_id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Buying_Center_Summary") -> where("Batch = '$batch_id'");
		$summaries = $query -> execute(array());
		return $summaries;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Buying_Center_Summary");
		$summaries = $query -> execute();
		return $summaries;
	}

	public function checkDuplicate($summary) {
		$query = Doctrine_Query::create() -> select("count(*) as Records") -> from("Buying_Center_Summary") -> where("Summary_Number = '$summary' and Adjustment != '1'");
		$summaries = $query -> execute();
		return $summaries[0] -> Records;
	}

	public function getSearchedSummary($summary) {
		$query = Doctrine_Query::create() -> select("*") -> from("Buying_Center_Summary") -> where("Summary_Number = '$summary'");
		$summary = $query -> execute();
		return $summary;
	}

}
