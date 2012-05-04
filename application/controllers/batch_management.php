<?php
class Batch_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function enter_batch($batch) {
		$batch_object = Transaction_Batch::getBatch($batch);
		if ($batch_object -> Transaction_Type_Object -> Indicator == "purchases") {
			//load purchases for this batch
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "input_disbursements") {
			$this -> session -> set_userdata(array("input_disbursement_batch" => $batch));
			//load input disbursements for this batch
			$url = "disbursement_management/listing/" . $batch;
			redirect($url);
		}
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$user = $this -> session -> userdata('user_id');
		$user_indicator = $this -> session -> userdata('user_indicator');
		if ($user_indicator == "general_supervisor") {
			$number_of_batches = Transaction_Batch::getTotalClosedBatches();
			$batches = Transaction_Batch::getPagedClosedBatches($offset, $items_per_page);
			$data['content_view'] = "list_supervisor_batches_v";
		}
		if ($user_indicator == "system_administrator") {
			$number_of_batches = Transaction_Batch::getTotalSystemBatches();
			$batches = Transaction_Batch::getPagedSystemBatches($offset, $items_per_page);
			$data['clerks'] = User::getActiveDataClerks();
			$data['content_view'] = "list_admin_batches_v";
		} else if ($user_indicator == "cash_supervisor") {
			$data['content_view'] = "list_supervisor_batches_v";
		} else if ($user_indicator == "purchases_supervisor") {
			$data['content_view'] = "list_supervisor_batches_v";
		} else if ($user_indicator == "inputs_supervisor") {
			$data['content_view'] = "list_supervisor_batches_v";
		} else {
			$number_of_batches = Transaction_Batch::getTotalBatches($user);
			$batches = Transaction_Batch::getPagedBatches($offset, $items_per_page, $user);
			$data['content_view'] = "list_batches_v";
		}

		if ($number_of_batches > $items_per_page) {
			$config['base_url'] = base_url() . "batch_management/listing/";
			$config['total_rows'] = $number_of_batches;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batches'] = $batches;
		$data['title'] = "Depots";

		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function change_ownership($batch, $new_owner) {
		$batch_object = Transaction_Batch::getBatch($batch);
		$status = $batch_object -> Status;
		//Check if this batch has already been posted. If so then just return the user to the batch listing
		if ($status == "2") {
			redirect("batch_management");
		}
		//change ownership
		$old_owner = $batch_object->User_Object->Name;
		$batch_object -> User = $new_owner;		
		$batch_object -> save(); 
		$new_owner_object = User::getUser($new_owner);
		$log = new System_Log();
		$details_desc = "{Transaction Type: '" . $batch_object -> Transaction_Type_Object -> Name . "', Batch ID '" . $batch . "', Old Owner: '".$old_owner."', New Owner: '".$new_owner_object->Name."'}";
		$log -> Log_Message = "Changed Batch Ownership " . $details_desc;
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> Log_Type = "2";
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function new_batch($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['transaction_types'] = Transaction_Type::getAll();
		$data['content_view'] = "add_batch_v";
		$data['quick_link'] = "new_batch";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function open_batch($batch) {
		$batch_object = Transaction_Batch::getBatch($batch);
		$status = $batch_object -> Status;
		//Check if this batch has already been posted. If so then just return the user to the batch listing
		if ($status == "2") {
			redirect("batch_management");
		}
		//Open the batch
		$batch_object -> Status = "0";
		$batch_object -> save();
		$this -> apply_to_transactions($batch, "0");
		$log = new System_Log();
		$details_desc = "{Transaction Type: '" . $batch_object -> Transaction_Type_Object -> Name . "'Batch ID '" . $batch . "'}";
		$log -> Log_Message = "Opened Batch Record " . $details_desc;
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> Log_Type = "2";
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function close_batch($batch) {
		$batch_object = Transaction_Batch::getBatch($batch);
		$status = $batch_object -> Status;
		//Check if this batch has already been posted. If so then just return the user to the batch listing
		if ($status == "2") {
			redirect("batch_management");
		}
		$batch_object -> Status = "1";
		$batch_object -> save();
		$previous_page = $this -> session -> userdata('old_url');
		$this -> apply_to_transactions($batch, "1");
		$log = new System_Log();
		$details_desc = "{Transaction Type: '" . $batch_object -> Transaction_Type_Object -> Name . "'Batch ID '" . $batch . "'}";
		$log -> Log_Message = "Closed Batch Record " . $details_desc;
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> Log_Type = "2";
		$log -> save();
		redirect($previous_page);
	}

	public function post_batch($batch) {
		$batch_object = Transaction_Batch::getBatch($batch);
		$status = $batch_object -> Status;
		$batch_object -> Status = "2";
		$batch_object -> Validated_By = $this -> session -> userdata('user_id');
		$batch_object -> save();
		$previous_page = $this -> session -> userdata('old_url');
		$this -> apply_to_transactions($batch, "2");
		$log = new System_Log();
		$details_desc = "{Transaction Type: '" . $batch_object -> Transaction_Type_Object -> Name . "'Batch ID '" . $batch . "'}";
		$log -> Log_Message = "Posted Batch Record " . $details_desc;
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> Log_Type = "2";
		$log -> save();
		redirect($previous_page);
	}

	public function apply_to_transactions($batch, $status) {
		$batch_object = Transaction_Batch::getBatch($batch);
		if ($batch_object -> Transaction_Type_Object -> Indicator == "purchases") {
			$purchases = Purchase::getBatchPurchases($batch);
			foreach ($purchases as $purchase) {
				$purchase -> Batch_Status = $status;
				$purchase -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "input_disbursements") {
			$disbursements = Disbursement::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> Batch_Status = $status;
				$disbursement -> save();
			}
		}

	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$transaction_batch = new Transaction_Batch();
			$transaction_batch -> Transaction_Type = $this -> input -> post("transaction_id");
			$transaction_batch -> User = $this -> session -> userdata('user_id');
			$transaction_batch -> Timestamp = date('U');
			$transaction_batch -> Status = "0";
			$transaction_batch -> save();
			$details_desc = "{Transaction Type: '" . $transaction_batch -> Transaction_Type_Object -> Name . "'}";
			$log -> Log_Type = "1";
			$log -> Log_Message = "Created Transaction Batch Record " . $details_desc;
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			redirect("batch_management/listing");
		} else {
			$this -> new_batch();
		}
	}

	public function delete_batch($id) {
		$batch_object = Transaction_Batch::getBatch($id);
		if ($batch_object -> Transaction_Type_Object -> Indicator == "purchases") {
			$purchases = Purchase::getBatchPurchases($batch);
			foreach ($purchases as $purchase) {
				$purchase -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "input_disbursements") {
			$disbursements = Disbursement::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> delete();
			}
		}
		$log = new System_Log();
		$log -> Log_Type = "3";
		$transaction_types = array("Input Disbursements", "Purchases");
		$log -> Log_Message = "Deleted Batch (plus all transactions in the batch) Record {Transaction Type: '" . $batch_object -> Transaction_Type_Object -> Name . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$batch_object -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function print_batch($batch_id) {
		$batch = Transaction_Batch::getBatch($batch_id);
		$transaction_type = $batch -> Transaction_Type_Object -> Indicator;
		$data_buffer = "";
		$report_footer = "";
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";

		//If they are input disbursements
		if ($transaction_type == "input_disbursements") {
			$total_inputs_value = 0;
			$disbursements = Disbursement::getBatchDisbursements($batch_id);
			$data_buffer .= "<tr><th>FGB</th><th>Invoice</th><th>Date</th><th>Farm Input</th><th>Quantity</th><th>Total Value</th><th>Season</th><th>Agent</th><th>Timestamp</th></tr>";
			foreach ($disbursements as $disbursement) {
				$data_buffer .= "<tr><td>" . $disbursement -> FBG_Object -> Group_Name . "</td><td>" . $disbursement -> Invoice_Number . "</td><td>" . $disbursement -> Date . "</td><td>" . $disbursement -> Farm_Input_Object -> Product_Name . "</td><td>" . $disbursement -> Quantity . "</td><td>" . $disbursement -> Total_Value . "</td><td>" . $disbursement -> Season . "</td><td>" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "</td><td>" . date("d/m/Y H:i:s", $disbursement -> Timestamp) . "</td></tr>";
				$total_inputs_value += $disbursement -> Total_Value;
			}
			$report_footer = "<div style='border-top:2px solid black;'><span><b>Total Value of Transactions: " . $batch_total . " </b></span></div>";
		}
		if ($transaction_type == "purchases") {
			$batch_gross_total = 0;
			$batch_net_total = 0;
			$batch_recoveries = 0;
			$purchases = Purchase::getBatchPurchases($batch_id);
			$data_buffer .= "<tr><th>FGB</th><th>DPN</th><th>Date</th><th>Depot</th><th>Quantity</th><th>Unit Price</th><th>Season</th><th>Loan Recovery</th><th>Farmer Reg. Fee</th><th>Other Recoveries</th><th>Buyer</th><th>Gross Value</th><th>Net Value</th></tr>";
			foreach ($purchases as $purchase) {
				$data_buffer .= "<tr><td>" . $purchase -> FBG_Object -> Group_Name . "</td><td>" . $purchase -> DPN . "</td><td>" . $purchase -> Date . "</td><td>" . $purchase -> Depot_Object -> Depot_Name . "</td><td>" . $purchase -> Quantity . "</td><td>" . $purchase -> Unit_Price . "</td><td>" . $purchase -> Season . "</td><td>" . $purchase -> Loan_Recovery . "</td><td>" . $purchase -> Farmer_Reg_Fee . "</td><td>" . $purchase -> Other_Recoveries . "</td><td>" . $purchase -> Buyer_Object -> Name . "</td><td>" . $purchase -> Gross_Value . "</td><td>" . $purchase -> Net_Value . "</td></tr>";
				$batch_gross_total += $purchase -> Gross_Value;
				$batch_net_total += $purchase -> Net_Value;
				$batch_recoveries += $purchase -> Loan_Recovery + $purchase -> Farmer_Reg_Fee + $purchase -> Other_Recoveries;
			}
			$report_footer = "<div style='border-top:2px solid black;'><span><b>Gross Value of Transactions: " . $batch_gross_total . " </b></span></div>";
			$report_footer .= "<div style='border-top:2px solid black;'><span><b>Total Recoveries: " . $batch_recoveries . " </b></span></div>";
			$report_footer .= "<div style='border-top:2px solid black;'><span><b>Net Value of Transactions: " . $batch_net_total . " </b></span></div>";
		}
		$data_buffer .= "</table>";
		$data_buffer .= $report_footer;
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $batch);

	}

	function generatePDF($data, $batch) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h2 style='text-align:center; text-decoration:underline;'>Alliance Ginneries</h2>";
		$html_title .= "<h1 style='text-align:center; text-decoration:underline;'>Batch Details (" . $batch -> Transaction_Type_Object -> Name . "," . $batch -> id . ") </h1>";
		$html_title .= "<h3 style='text-align:center;'> entered by: " . $batch -> User_Object -> Name . "</h3>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('Regional Summaries');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Batch Details.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('transaction_id', 'Transaction Type', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Batch Management";
		$data['link'] = "batch_management";

		$this -> load -> view("demo_template", $data);
	}

}
