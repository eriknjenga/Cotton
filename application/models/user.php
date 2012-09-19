<?php
class User extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 100);
		$this -> hasColumn('Username', 'varchar', 12);
		$this -> hasColumn('Password', 'varchar', 32);
		$this -> hasColumn('Access_Level', 'varchar', 10);
		$this -> hasColumn('Phone_Number', 'varchar', 50);
		$this -> hasColumn('Created_By', 'varchar', 10);
		$this -> hasColumn('Email_Address', 'varchar', 50);
		$this -> hasColumn('Time_Created', 'varchar', 32);
		$this -> hasColumn('Active', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('user');
		$this -> hasMutator('Password', '_encrypt_password');
		$this -> hasOne('Access_Level as Access', array('local' => 'Access_Level', 'foreign' => 'id'));
		$this -> hasOne('User as Creator', array('local' => 'Added_By', 'foreign' => 'id'));
	}

	protected function _encrypt_password($value) {
		$this -> _set('Password', md5($value));
	}

	public function login($username, $password) {

		$query = Doctrine_Query::create() -> select("*") -> from("User") -> where("Username = '" . $username . "' and Active = '1'");
		$user = $query -> fetchOne();
		if ($user) {

			$user2 = new User();
			$user2 -> Password = $password;

			if ($user -> Password == $user2 -> Password) {
				return $user;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	public function getTotalUsers() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Users") -> from("User") -> where("Active = '1'");
		$total = $query -> execute();
		return $total[0]['Total_Users'];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("User") -> where("Active = '1'");
		$users = $query -> execute();
		return $users;
	}

	public function getPagedUsers($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("User") -> where("Active = '1'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$users = $query -> execute(array());
		return $users;
	}

	public function getUser($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("User") -> where("id = '$id'");
		$user = $query -> execute();
		return $user[0];
	}

	public static function userExists($username) {
		$query = Doctrine_Query::create() -> select("*") -> from("User") -> where("Username = '$username'");
		$user = $query -> execute();
		if (isset($user[0])) {

			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getActiveDataClerks() {
		$query = Doctrine_Query::create() -> select("*") -> from("User u") -> where("u.Access.Indicator = 'data_entry' and Active = '1'");
		$clerks = $query -> execute();
		return $clerks;
	}

	public function getSearchedUser($search_value) {
		$query = Doctrine_Query::create() -> select("*") -> from("User") -> where("Name like '%$search_value%' or Username like '%$search_value%' or Phone_Number like '%$search_value%' or Email_Address like '%$search_value%'");
		$results = $query -> execute();
		return $results;
	}

}
