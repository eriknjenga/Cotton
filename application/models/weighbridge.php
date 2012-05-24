<?php
class Weighbridge extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Vehicle_Number', 'varchar', 50);
		$this -> hasColumn('Weighing_Mode', 'varchar',5);
		$this -> hasColumn('Weighing_Type', 'varchar', 5);
		$this -> hasColumn('Charge_Type', 'varchar', 5);
		$this -> hasColumn('Vehicle_Type', 'varchar',5);
		$this -> hasColumn('Buying_Center_Code', 'varchar', 20);
		$this -> hasColumn('Product_Code', 'varchar', 10);
		$this -> hasColumn('Cell', 'text');
		$this -> hasColumn('Category', 'text');
		$this -> hasColumn('Containment', 'text');
		$this -> hasColumn('Transporter', 'varchar',50);
		$this -> hasColumn('Destination', 'text');
		$this -> hasColumn('UDF_1', 'varchar', 20);
		$this -> hasColumn('Number_Of_Bags', 'varchar',10);
		$this -> hasColumn('Conversion', 'text');
		$this -> hasColumn('Adjusted_First_Weight', 'varchar', 20);
		$this -> hasColumn('Adjusted_Second_Weight', 'varchar',20);
		$this -> hasColumn('UDF_6', 'varchar', 20);
		$this -> hasColumn('UDF_7', 'varchar', 20);
		$this -> hasColumn('UDF_8', 'varchar',20);
		$this -> hasColumn('Station_ID', 'text');
		$this -> hasColumn('First_Weight', 'varchar', 20);
		$this -> hasColumn('First_Weight_Unit', 'varchar',10);
		$this -> hasColumn('First_Weight_Date', 'varchar', 15);
		$this -> hasColumn('First_Weight_Time', 'varchar', 10);
		$this -> hasColumn('First_Weight_Consec_No', 'varchar', 10);
		$this -> hasColumn('First_Weight_ID', 'varchar',10);
		$this -> hasColumn('Second_Weight', 'varchar', 20);
		$this -> hasColumn('Second_Weight_Unit', 'varchar', 10);
		$this -> hasColumn('Second_Weight_Date', 'varchar',15);
		$this -> hasColumn('Second_Weight_Time', 'varchar', 10);
		$this -> hasColumn('Second_Weight_Consec_No', 'varchar', 10);
		$this -> hasColumn('Second_Weight_Id', 'varchar',10);
		$this -> hasColumn('Net_Weight', 'varchar', 20);
		$this -> hasColumn('Adjusted_Net_Weight', 'varchar', 20);
		$this -> hasColumn('Transaction_Date', 'varchar',15);
		$this -> hasColumn('Transaction_Time', 'varchar', 10);
		$this -> hasColumn('Ticket_Number', 'varchar', 20);
		$this -> hasColumn('Transaction_Number', 'varchar',20);
		$this -> hasColumn('Total_Charge', 'varchar', 10);
		$this -> hasColumn('Operator', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('weighbridge');
	}

}
