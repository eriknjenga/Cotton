<?php
class Batch_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$user = $this -> session -> userdata('user_id');
		$number_of_batches = Transaction_Batch::getTotalBatches($user);
		$batches = Transaction_Batch::getPagedBatches($offset, $items_per_page, $user);

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
		$data['content_view'] = "list_batches_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_batch($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_batch_v";
		$data['quick_link'] = "new_batch";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$transaction_batch = new Transaction_Batch();
			$log -> Log_Type = "1";
			$transaction_types = array("Input Disbursements", "Purchases");
			$details_desc = "{Transaction Type: '" . $transaction_types[$this -> input -> post("transaction_id")] . "'}";
			$log -> Log_Message = "Created Transaction Batch Record " . $details_desc;
			$transaction_batch -> Transaction_Type = $this -> input -> post("transaction_id");
			$transaction_batch -> User = $this -> session -> userdata('user_id');
			$transaction_batch -> Timestamp = date('U');
			$transaction_batch -> Status = "0";
			$transaction_batch -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			redirect("batch_management/listing");
		} else {
			$this -> new_batch();
		}
	}

	public function delete_batch($id) {
		$batch = Transaction_Batch::getBatch($id);
		$batch_id = $batch -> id;
		$transaction_type = $batch -> Transaction_Type;
		//Input disbursements
		if ($transaction_type == 0) {
			$disbursements = Disbursement::getBatchDisbursements($batch_id);
			foreach ($disbursements as $disbursement) {
				$disbursement -> delete();
			}
		}
		//Purchases
		else if ($transaction_type == 1) {
			$purchases = Purchase::getBatchPurchases($batch_id);
			foreach ($purchases as $purchase) {
				$purchase -> delete();
			}
		}
		$log = new System_Log();
		$log -> Log_Type = "3";
		$transaction_types = array("Input Disbursements", "Purchases");
		$log -> Log_Message = "Deleted Batch (plus all transactions in the batch) Record {Transaction Type: '" . $transaction_types[$batch -> Transaction_Type] . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$batch -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function print_batch($batch_id) {
		$batch = Transaction_Batch::getBatch($batch_id);
		$transaction_type = $batch -> Transaction_Type;
		$data_buffer = "";
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";

		//If they are input disbursements
		if ($transaction_type == "0") {
			$disbursements = Disbursement::getBatchDisbursements($batch_id);
			$data_buffer .= "<tr><th>FGB</th><th>Invoice</th><th>Date</th><th>Farm Input</th><th>Quantity</th><th>Total Value</th><th>Season</th><th>Agent</th><th>Timestamp</th></tr>";
			foreach ($disbursements as $disbursement) {
				$data_buffer .= "<tr><td>" . $disbursement -> FBG_Object -> Group_Name . "</td><td>" . $disbursement -> Invoice_Number . "</td><td>" . $disbursement -> Date . "</td><td>" . $disbursement -> Farm_Input_Object->Product_Name . "</td><td>" . $disbursement -> Quantity . "</td><td>" . $disbursement -> Total_Value . "</td><td>" . $disbursement -> Season . "</td><td>" . $disbursement -> Agent_Object->First_Name." ".$disbursement -> Agent_Object->Surname . "</td><td>" . date("d/m/Y H:i:s",$disbursement ->Timestamp) . "</td></tr>";
			}
		} 
		$data_buffer .= "</table>";
		$this -> generatePDF($data_buffer,$batch);

	}

	function generatePDF($data,$batch) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h2 style='text-align:center; text-decoration:underline;'>Alliance Ginneries</h2>";
		$transaction_types = array("Input Disbursements","Purchases");
		$html_title .= "<h1 style='text-align:center; text-decoration:underline;'>Batch Details (".$transaction_types[$batch->Transaction_Type].",".$batch->id.") </h1>";
		$html_title .= "<h3 style='text-align:center;'> entered by: " . $batch->User_Object->Name . "</h3>";

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
