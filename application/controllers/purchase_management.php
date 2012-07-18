<?php
class Purchase_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($batch, $offset = 0) {
		$items_per_page = 20;
		$number_of_purchases = Purchase::getTotalPurchases($batch);
		$purchases = Purchase::getPagedPurchases($batch, $offset, $items_per_page);
		if ($number_of_purchases > $items_per_page) {
			$config['base_url'] = base_url() . "purchase_management/listing/" . $batch . "/";
			$config['total_rows'] = $number_of_purchases;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batch'] = $batch;
		$data['purchases'] = $purchases;
		$data['title'] = "Cotton Disbursements";
		$data['content_view'] = "list_purchases_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function record_purchase($depot) {
		$depot_object = Depot::getDepot($depot);
		if ($depot_object -> Deleted == '2') {
			$data['depot'] = $depot_object;
			$data['content_view'] = "chief_accountant_authorization_v";
			$data['request_url'] = "purchase_management/authorize_purchase/".$depot;
			$data['message'] = "This Buying Center is marked as closed";
			$data['quick_link'] = "new_purchase";
			$data['scripts'] = array("validationEngine-en.js", "validator.js", "jquery.ui.autocomplete.js");
			$data['styles'] = array("validator.css");
			$this -> base_params($data);
		} else {
			$data['depot'] = $depot_object;
			$this -> session -> set_userdata(array('saved_depot' => $depot));
			$this -> new_purchase($data);
		}
	}

	public function authorization_failed($depot) {
		$depot_object = Depot::getDepot($depot);
		$data['depot'] = $depot_object;
		$data['content_view'] = "chief_accountant_authorization_v";
		$data['request_url'] = "purchase_management/authorize_purchase/".$depot;
		$data['message'] = "Authorization Failed. This could be caused by, Invalid credentials, wrong access level or the user has been disabled. Consult your Administrator and try again!";
		$data['quick_link'] = "new_purchase";
		$data['scripts'] = array("validationEngine-en.js", "validator.js", "jquery.ui.autocomplete.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function authorize_purchase($depot) {
		$depot_object = Depot::getDepot($depot);
		$data['depot'] = $depot_object;
		$this -> session -> set_userdata(array('saved_depot' => $depot));
		$this -> new_purchase($data);
	}

	public function new_purchase($data = null) {
		$batch = $this -> session -> userdata('purchases_batch');
		if (strlen($batch) == 0) {
			redirect("batch_management/no_batch");
		}
		if ($data == null) {
			$this->search_depot();
		}
		$data['batch_information'] = "You are entering records into batch number: <b>" . $this -> session -> userdata('purchases_batch') . "</b>";
		$depot_object = $data['depot'];
		$depot_zone =  $depot_object->Village_Object->Ward_Object->Region_Object->id;
		$data['prices'] = Cotton_Price::getCottonPrices($depot_zone);
		$data['content_view'] = "add_purchase_v";
		$data['quick_link'] = "new_purchase";
		$data['scripts'] = array("validationEngine-en.js", "validator.js", "jquery.ui.autocomplete.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function search_depot() {
		$batch = $this -> session -> userdata('purchases_batch');
		if (strlen($batch) == 0) {
			redirect("batch_management/no_batch");
		}
		$data['content_view'] = "search_depot_v";
		$data['link'] = "purchase_management";
		$data['quick_link'] = "search_depot";
		$data['search_title'] = "Search For an Buying Center to Record Purchases For";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> load -> view("demo_template", $data);
	}

	public function edit_purchase($id) {
		$purchase = Purchase::getPurchase($id);
		$fbg = $purchase -> FBG;
		$recipient = FBG::getFbg($fbg);
		$data['disbursements'] = Disbursement::getFBGDisbursements($fbg);
		$data['purchase'] = $purchase;
		$this -> new_purchase($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$dpn = $this -> input -> post("dpn");
			$date = $this -> input -> post("date");
			$quantity = $this -> input -> post("quantity");
			$total_value = $this -> input -> post("purchased_value");
			$season = $this -> input -> post("season");
			$fbg = $this -> input -> post("fbg_id");
			$depot = $this -> input -> post("depot");
			$loan_recovery = $this -> input -> post("loan_recovery");
			$farmer_registration = $this -> input -> post("farmer_registration");
			$other_recoveries = $this -> input -> post("other_recoveries");
			$buyer = $this -> input -> post("buyer");
			$net_value = $this -> input -> post("net_value");
			$price = $this -> input -> post("price");
			$batch = $this -> session -> userdata('purchases_batch');

			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$log -> Log_Type = "2";
				$purchase = Purchase::getPurchase($editing);
				$message = "Edited Purchase Record From {FGB: '" . $purchase -> FBG_Object -> Group_Name . "' DPN: '" . $purchase -> DPN . "' Date: '" . $purchase -> Date . "' $purchase: '" . $purchase -> Depot_Object -> Depot_Name . "' Quantity: '" . $purchase -> Quantity . "' Unit Price: '" . $purchase -> Unit_Price . "' Season: '" . $purchase -> Season . "' Loan Recovery: '" . $purchase -> Loan_Recovery . "' Farmer Registration Fee: '" . $purchase -> Farmer_Reg_Fee . "' Other Recoveries: '" . $purchase -> Other_Recoveries . "' Buyer: '" . $purchase -> Buyer_Object -> Name . "' Net_Value: '" . $purchase -> Net_Value . "' Gross_Value: '" . $purchase -> Gross_Value . "'} to ";
			} else {
				$log -> Log_Type = "1";
				$purchase = new Purchase();
				$message = "Created New Purchase Record ";
			}
			$purchase -> clearRelated();
			$purchase -> FBG = $fbg;
			$purchase -> DPN = $dpn;
			$purchase -> Date = $date;
			$purchase -> Depot = $depot;
			$purchase -> Quantity = $quantity;
			$purchase -> Unit_Price = $price;
			$purchase -> Gross_Value = $total_value;
			$purchase -> Net_Value = $net_value;
			$purchase -> Season = $season;
			$purchase -> Loan_Recovery = $loan_recovery;
			$purchase -> Farmer_Reg_Fee = $farmer_registration;
			$purchase -> Other_Recoveries = $other_recoveries;
			$purchase -> Buyer = $buyer;
			$purchase -> Batch = $batch;
			$purchase -> Timestamp = date('U');
			$purchase -> save();
			$purchase = Purchase::getPurchase($purchase -> id);
			$message .= "{FGB: '" . $purchase -> FBG_Object -> Group_Name . "' DPN: '" . $purchase -> DPN . "' Date: '" . $purchase -> Date . "' $purchase: '" . $purchase -> Depot_Object -> Depot_Name . "' Quantity: '" . $purchase -> Quantity . "' Unit Price: '" . $purchase -> Unit_Price . "' Season: '" . $purchase -> Season . "' Loan Recovery: '" . $purchase -> Loan_Recovery . "' Farmer Registration Fee: '" . $purchase -> Farmer_Reg_Fee . "' Other Recoveries: '" . $purchase -> Other_Recoveries . "' Buyer: '" . $purchase -> Buyer_Object -> Name . "' Net_Value: '" . $purchase -> Net_Value . "' Gross_Value: '" . $purchase -> Gross_Value . "'}";
			$log -> Log_Message = $message;
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();

			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New From Buying Center") {
				$saved_depot = $this -> session -> userdata('saved_depot');
				$url = "purchase_management/record_purchase/" . $saved_depot;
				redirect($url);
			} else if ($submit_button == "Save & View List") {
				$batch = $this -> session -> userdata('purchases_batch');
				$link = "purchase_management/listing/" . $batch;
				redirect($link);
			}
		} else {
			echo "Could not save. Error occured!";
		}
	}

	public function delete_purchase($id) {
		$purchase = Purchase::getPurchase($id);
		$purchase -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Purchase Record {FGB: '" . $purchase -> FBG_Object -> Group_Name . "' DPN: '" . $purchase -> DPN . "' Date: '" . $purchase -> Date . "' $purchase: '" . $purchase -> Depot_Object -> Depot_Name . "' Quantity: '" . $purchase -> Quantity . "' Unit Price: '" . $purchase -> Unit_Price . "' Season: '" . $purchase -> Season . "' Loan Recovery: '" . $purchase -> Loan_Recovery . "' Farmer Registration Fee: '" . $purchase -> Farmer_Reg_Fee . "' Other Recoveries: '" . $purchase -> Other_Recoveries . "' Buyer: '" . $purchase -> Buyer_Object -> Name . "' Net_Value: '" . $purchase -> Net_Value . "' Gross_Value: '" . $purchase -> Gross_Value . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('dpn', 'Daily Purchases Number', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('depot', 'Buying Center', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('date', 'Date', 'trim|required|max_length[100]|xss_clean');
		return $this -> form_validation -> run();
	}

	function getDailyTrend() {
		$this -> load -> database();
		$sql = "SELECT sum(gross_value) as total_purchases,date FROM `purchase` p where batch_status = '2'   group by date order by str_to_date(p.date,'%m/%d/%Y') asc limit 7";
		$query = $this -> db -> query($sql);
		$purchasing_data = $query -> result_array();
		$chart = '<chart caption="Daily Purchases Trend" subcaption="For the past 7 days" xAxisName="Day" yAxisName="Purchases (Tsh.)" showValues="0" alternateHGridColor="FCB541" alternateHGridAlpha="20" divLineColor="FCB541" divLineAlpha="50" canvasBorderColor="666666" baseFontColor="666666" lineColor="FCB541">';
		foreach ($purchasing_data as $data) {
			$chart .= '<set label="' . $data['date'] . '" value="' . $data['total_purchases'] . '"/>';
		}
		$chart .= '
		<styles>
<definition>
<style name="Anim1" type="animation" param="_xscale" start="0" duration="1"/>
<style name="Anim2" type="animation" param="_alpha" start="0" duration="0.6"/>
<style name="DataShadow" type="Shadow" alpha="40"/>
</definition>
<application>
<apply toObject="DIVLINES" styles="Anim1"/>
<apply toObject="HGRID" styles="Anim2"/>
<apply toObject="DATALABELS" styles="DataShadow,Anim2"/>
</application>
</styles>
		</chart>';
		echo $chart;
	}

	public function base_params($data) {
		$data['link'] = "purchase_management";

		$this -> load -> view("demo_template", $data);
	}

}
