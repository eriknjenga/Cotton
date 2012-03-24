<?php
class Quick_Menu extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Menu_Text', 'varchar', 50);
		$this -> hasColumn('Menu_Url', 'varchar', 100);
		$this -> hasColumn('Description', 'text');
		$this -> hasColumn('Indicator', 'varchar', 100);
	}

	public function setUp() {
		$this -> setTableName('quick_menu');
	}

	public static function getAllHydrated() {
		$query = Doctrine_Query::create() -> select("Menu_Text, Menu_Url, Description") -> from("Quick_Menu");
		$menus = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $menus;
	}

}
