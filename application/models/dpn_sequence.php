<?php
class DPN_Sequence extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Depot', 'varchar', 10);
		$this -> hasColumn('First', 'varchar', 15);
		$this -> hasColumn('Last', 'varchar', 15);
		$this -> hasColumn('Season', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('dpn_sequence');
		$this -> hasOne('Depot as Depot_Object', array('local' => 'Depot', 'foreign' => 'id'));
	}

	public function getTotalSequences() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Sequences") -> from("DPN_Sequence");
		$total = $query -> execute();
		return $total[0]['Total_Sequences'];
	}

	public function getPagedSequences($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("DPN_Sequence") -> offset($offset) -> limit($items)->orderBy("id Desc");
		$sequences = $query -> execute(array());
		return $sequences;
	}

	public function getSequence($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("DPN_Sequence") -> where("id = '$id'");
		$sequence = $query -> execute();
		return $sequence[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("DPN_Sequence");
		$sequences = $query -> execute();
		return $sequences;
	}

}
