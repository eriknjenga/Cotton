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
		$this -> open_batch($batch);
		if ($batch_object -> Transaction_Type_Object -> Indicator == "purchases") {
			$this -> session -> set_userdata(array("purchases_batch" => $batch));
			$url = "purchase_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "input_disbursements") {
			$this -> session -> set_userdata(array("input_disbursement_batch" => $batch));
			$url = "disbursement_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "agent_input_disbursements") {
			$this -> session -> set_userdata(array("agent_input_disbursement_batch" => $batch));
			$url = "agent_input_issue_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "buying_center_receipts") {
			$this -> session -> set_userdata(array("buying_center_receipt_batch" => $batch));
			$url = "buying_center_receipt_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cash_receipts") {
			$this -> session -> set_userdata(array("cash_receipt_batch" => $batch));
			$url = "cash_receipt_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cihc") {
			$this -> session -> set_userdata(array("cihc_batch" => $batch));
			$url = "cash_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cihb") {
			$this -> session -> set_userdata(array("cihb_batch" => $batch));
			$url = "field_cash_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "input_transfers") {
			$this -> session -> set_userdata(array("input_transfer_batch" => $batch));
			$url = "region_input_issue_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "mopping_payments") {
			$this -> session -> set_userdata(array("mopping_payment_batch" => $batch));
			$url = "mopping_payment_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "loan_recovery_receipts") {
			$this -> session -> set_userdata(array("loan_recovery_receipt_batch" => $batch));
			$url = "loan_recovery_receipt_management/listing/" . $batch;
			redirect($url);
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "buying_center_summaries") {
			$this -> session -> set_userdata(array("buying_center_summary_batch" => $batch));
			$url = "buying_center_summary_management/listing/" . $batch;
			redirect($url);
		}
	}

	public function search() {
		$search_term = $this -> input -> post("batch_id");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$user = $this -> session -> userdata('user_id');
		$user_indicator = $this -> session -> userdata('user_indicator');
		if ($user_indicator == "general_supervisor") {
			$batch = Transaction_Batch::getSearchedSupervisorBatch($db_search_term, $user);
			$data['content_view'] = "list_supervisor_batches_v";
		} else if ($user_indicator == "system_administrator") {
			$batch = Transaction_Batch::getSearchedAdminBatch($db_search_term, $user);
			$data['clerks'] = User::getActiveDataClerks();
			$data['content_view'] = "list_admin_batches_v";
		} else {
			$batch = Transaction_Batch::getSearchedUserBatch($db_search_term, $user);
			$data['content_view'] = "list_batches_v";
		}
		$data['batches'] = $batch;
		$this -> base_params($data);
	}

	public function listing($status = "N", $offset = 0) {
		$items_per_page = 10;
		$user = $this -> session -> userdata('user_id');
		$user_indicator = $this -> session -> userdata('user_indicator');

		if ($user_indicator == "general_supervisor") {
			$number_of_batches = Transaction_Batch::getTotalClosedBatches($status);
			$batches = Transaction_Batch::getPagedClosedBatches($offset, $items_per_page, $status);
			$data['content_view'] = "list_supervisor_batches_v";
		} else if ($user_indicator == "system_administrator") {
			$number_of_batches = Transaction_Batch::getTotalSystemBatches($status);
			$batches = Transaction_Batch::getPagedSystemBatches($offset, $items_per_page, $status);
			$data['clerks'] = User::getActiveDataClerks();
			$data['content_view'] = "list_admin_batches_v";
		} else if ($user_indicator == "cash_supervisor") {
			$data['content_view'] = "list_supervisor_batches_v";
		} else if ($user_indicator == "purchases_supervisor") {
			$data['content_view'] = "list_supervisor_batches_v";
		} else if ($user_indicator == "inputs_supervisor") {
			$data['content_view'] = "list_supervisor_batches_v";
		} else {
			$number_of_batches = Transaction_Batch::getTotalBatches($user, $status);
			$batches = Transaction_Batch::getPagedBatches($offset, $items_per_page, $user, $status);
			$data['content_view'] = "list_batches_v";
		}

		if ($number_of_batches > $items_per_page) {
			$config['base_url'] = base_url() . "batch_management/listing/" . $status . "/";
			$config['total_rows'] = $number_of_batches;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batches'] = $batches;
		$data['title'] = "Depots";
		$data['batch_listing'] = $status;
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
		$old_owner = $batch_object -> User_Object -> Name;
		$batch_object -> User = $new_owner;
		$batch_object -> save();
		$new_owner_object = User::getUser($new_owner);
		$log = new System_Log();
		$details_desc = "{Transaction Type: '" . $batch_object -> Transaction_Type_Object -> Name . "', Batch ID '" . $batch . "', Old Owner: '" . $old_owner . "', New Owner: '" . $new_owner_object -> Name . "'}";
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

	public function no_batch() {
		$data = array();
		$data['content_view'] = "no_batch_v";
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
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
		//redirect("batch_management");
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
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
		//redirect("batch_management");
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
			$farmer_disbursements = Farmer_Input::getBatchDisbursements($batch);
			foreach ($farmer_disbursements as $farmer_disbursement) {
				$farmer_disbursement -> Batch_Status = $status;
				$farmer_disbursement -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "agent_input_disbursements") {
			$disbursements = Agent_Input_Issue::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> Batch_Status = $status;
				$disbursement -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "buying_center_receipts") {
			$receipts = Buying_Center_Receipt::getBatchReceipts($batch);
			foreach ($receipts as $receipt) {
				$receipt -> Batch_Status = $status;
				$receipt -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cash_receipts") {
			$receipts = Cash_Receipt::getBatchReceipts($batch);
			foreach ($receipts as $receipt) {
				$receipt -> Batch_Status = $status;
				$receipt -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cihc") {
			$disbursements = Cash_Disbursement::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> Batch_Status = $status;
				$disbursement -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cihb") {
			$disbursements = Field_Cash_Disbursement::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> Batch_Status = $status;
				$disbursement -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "input_transfers") {
			$disbursements = Region_Input_Issue::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> Batch_Status = $status;
				$disbursement -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "mopping_payments") {
			$payments = Mopping_Payment::getBatchPayments($batch);
			foreach ($payments as $payment) {
				$payment -> Batch_Status = $status;
				$payment -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "loan_recovery_receipts") {
			$receipts = Loan_Recovery_Receipt::getBatchReceipts($batch);
			foreach ($receipts as $receipt) {
				$receipt -> Batch_Status = $status;
				$receipt -> save();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "buying_center_summaries") {
			$summaries = Buying_Center_Summary::getBatchSummaries($batch);
			foreach ($summaries as $summary) {
				$summary -> Batch_Status = $status;
				$summary -> save();
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
			//Open the batch
			$this -> enter_batch($transaction_batch -> id);
			redirect("batch_management/listing");
		} else {
			$this -> new_batch();
		}
	}

	public function delete_batch($batch) {
		$batch_object = Transaction_Batch::getBatch($batch);
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
			$farmer_disbursements = Farmer_Input::getBatchDisbursements($batch);
			foreach ($farmer_disbursements as $farmer_disbursement) {
				$farmer_disbursement -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "agent_input_disbursements") {
			$disbursements = Agent_Input_Issue::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "buying_center_receipts") {
			$receipts = Buying_Center_Receipt::getBatchReceipts($batch);
			foreach ($receipts as $receipt) {
				$receipt -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cash_receipts") {
			$receipts = Cash_Receipt::getBatchReceipts($batch);
			foreach ($receipts as $receipt) {
				$receipt -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cihc") {
			$disbursements = Cash_Disbursement::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "cihb") {
			$disbursements = Field_Cash_Disbursement::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "input_transfers") {
			$disbursements = Region_Input_Issue::getBatchDisbursements($batch);
			foreach ($disbursements as $disbursement) {
				$disbursement -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "mopping_payments") {
			$payments = Mopping_Payment::getBatchPayments($batch);
			foreach ($payments as $payment) {
				$payment -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "loan_recovery_receipts") {
			$receipts = Loan_Recovery_Receipt::getBatchReceipts($batch);
			foreach ($receipts as $receipt) {
				$receipt -> delete();
			}
		} else if ($batch_object -> Transaction_Type_Object -> Indicator == "buying_center_summaries") {
			$summaries = Buying_Center_Summary::getBatchSummaries($batch);
			foreach ($summaries as $summary) {
				$summary -> delete();
			}
		}
		$log = new System_Log();
		$log -> Log_Type = "3";
		$transaction_types = array("Input Disbursements", "Purchases");
		$log -> Log_Message = "Deleted Batch (plus all transactions in the batch) Record {Transaction Type: '" . $batch_object -> Transaction_Type_Object -> Name . "', Batch ID: '" . $batch . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$batch_object -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function print_batch($type, $batch_id) {
		$batch = Transaction_Batch::getBatch($batch_id);
		$transaction_type = $batch -> Transaction_Type_Object -> Indicator;
		$report_footer = "";
		$total_transactions = 0;
		$class = "";
		$batch_name = "Batch Details";
		$page_layout = "A4";
		//echo the start of the table
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			}
			table.data-table td {
			width: 50px !important;
			font-size:11 !important;
			margin: 0;
			}
			table.data-table th {
			width: 50px;
			font-size:11 !important;
			}
			.amount{
				text-align:right;
			}.adjustment{
				color: blue;
			}
			.wrong{
				color: red;
			}
			.center{
				text-align:center;
			}
			</style>
			";
		$excel_buffer = "";
		$data_buffer .= "<table class='data-table'>";

		//If they are input disbursements
		if ($transaction_type == "input_disbursements") {
			$batch_name = "Input Disbursements Batch " . $batch_id;
			$total_inputs_value = 0;
			$disbursements = Disbursement::getBatchDisbursements($batch_id);
			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>FGB</th><th>FBG Number</th><th>Invoice</th><th>Date</th><th>Farm Input</th><th>Quantity</th><th>Total Value</th><th>Agent</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Input Disbursements Batch, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "FGB\tFBG Number\tInvoice\tDate\tFarm Input\tQuantity\tTotal Value\tAgent\t\n";
			}

			foreach ($disbursements as $disbursement) {
				$formatted_date = date('d/m/Y', strtotime($disbursement -> Date));
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $disbursement -> FBG_Object -> Group_Name . "</td><td class='center'>" . $disbursement -> FBG_Object -> CPC_Number . "</td><td class='center'>" . $disbursement -> Invoice_Number . "</td><td class='center'>" . $formatted_date . "</td><td class='center'>" . $disbursement -> Farm_Input_Object -> Product_Name . "</td><td class='amount'>" . number_format($disbursement -> Quantity + 0) . "</td><td class='amount'>" . number_format($disbursement -> Total_Value + 0) . "</td> <td>" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $disbursement -> FBG_Object -> Group_Name . "\t" . $disbursement -> FBG_Object -> CPC_Number . "\t" . $disbursement -> Invoice_Number . "\t" . $formatted_date . "\t" . $disbursement -> Farm_Input_Object -> Product_Name . "\t" . $disbursement -> Quantity . "\t" . $disbursement -> Total_Value . "\t" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "\t\n";
				}

				$total_inputs_value += $disbursement -> Total_Value;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_inputs_value + 0) . "</b></td><td>-</td><td>-</td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals: \t-\t-\t-\t-\t-\t" . $total_inputs_value . "\t-\t-\t\n";
			}

		}
		if ($transaction_type == "purchases") {
			$batch_name = "Purchases Batch " . $batch_id;
			$page_layout = "A4-L";
			$batch_gross_total = 0;
			$batch_net_total = 0;
			$farmer_reg_total = 0;
			$other_recovery_total = 0;
			$loan_recovery_total = 0;
			$quantity_total = 0;
			$batch_free_farmer_quantity = 0;
			$batch_free_farmer_value = 0;
			$system_total_value = 0;
			$system_total_quantity = 0;
			$captured_total_value = 0;
			$captured_total_quantity = 0;
			$purchases = Purchase::getBatchPurchases($batch_id);
			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>FGB</th><th>DPN</th><th>Date</th><th>BC</th><th>BC Code</th><th>FBG Quantity</th><th>Unit Price</th><th>Loan Recovery</th><th>Farmer Reg. Fee</th><th>Other Recoveries</th><th>Buyer</th><th>Gross Value</th><th>Net Value</th><th>Free Farmer Quantity</th><th>Free Farmer Value</th><th>System Total Qty.</th><th>System Total Value</th><th>Captured Total Qty.</th><th>Captured Total Value</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Purchases, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "FGB\tDPN\tDate\tBC\tBC Code\tFBG Quantity\tUnit Price\tLoan Recovery\tFarmer Reg. Fee\tOther Recoveries\tBuyer\tGross Value\tNet Value\tFree Farmer Quantity\tFree Farmer Value\tSystem Total Qty.\tSystem Total Value\tCaptured Total Qty.\tCaptured Total Value\t\n";
			}
			foreach ($purchases as $purchase) {
				$total_transactions++;
				if ($purchase -> Adjustment == "1") {
					$class = "(a)";
				} else {
					$class = "";
				}
				$formatted_date = date('d/m/Y', strtotime($purchase -> Date));
				$total_system_quantity = ($purchase -> Quantity + $purchase -> Free_Farmer_Quantity);
				$total_system_value = ($purchase -> Gross_Value + $purchase -> Free_Farmer_Value);
				$total_captured_quantity = ($purchase -> Quantity + $purchase -> Grand_Total_Quantity);
				$total_captured_value = ($purchase -> Gross_Value + $purchase -> Grand_Total_Value);
				$quantity_class = "none";
				$value_class = "none";
				if ($total_captured_quantity != $total_system_quantity) {
					$quantity_class = "wrong";
				} else {
					$quantity_class = "none";
				}
				if ($total_captured_value != $total_system_value) {
					$value_class = "wrong";
				} else {
					$value_class = "none";
				}

				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $purchase -> FBG_Object -> Group_Name . "</td><td class='$class center'>" . $purchase -> DPN . " " . $class . "</td><td class='center'>" . $formatted_date . "</td><td class='center'>" . $purchase -> Depot_Object -> Depot_Name . "</td><td class='center'>" . $purchase -> Depot_Object -> Depot_Code . "</td><td class='amount'>" . number_format($purchase -> Quantity + 0) . "</td><td class='amount'>" . number_format($purchase -> Unit_Price + 0) . "</td><td class='amount'>" . number_format($purchase -> Loan_Recovery) . "</td><td class='amount'>" . number_format($purchase -> Farmer_Reg_Fee) . "</td><td class='amount'>" . number_format($purchase -> Other_Recoveries) . "</td><td class='center'>" . $purchase -> Buyer_Object -> Name . "</td><td class='amount'>" . number_format($purchase -> Gross_Value + 0) . "</td><td class='amount'>" . number_format($purchase -> Net_Value + 0) . "</td><td class='amount'>" . number_format($purchase -> Free_Farmer_Quantity + 0) . "</td><td class='amount'>" . number_format($purchase -> Free_Farmer_Value + 0) . "</td><td class='amount'>" . number_format($total_system_quantity) . "</td><td class='amount'>" . number_format($total_system_value) . "</td><td class='amount $quantity_class'>" . number_format($total_captured_quantity) . "</td><td class='amount $value_class'>" . number_format($total_captured_value) . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $purchase -> FBG_Object -> Group_Name . "\t" . $purchase -> DPN . " " . $class . "\t" . $formatted_date . "\t" . $purchase -> Depot_Object -> Depot_Name . "\t" . $purchase -> Depot_Object -> Depot_Code . "\t" . $purchase -> Quantity . "\t" . $purchase -> Unit_Price . "\t" . $purchase -> Loan_Recovery . "\t" . $purchase -> Farmer_Reg_Fee . "\t" . $purchase -> Other_Recoveries . "\t" . $purchase -> Buyer_Object -> Name . "\t" . $purchase -> Gross_Value . "\t" . $purchase -> Net_Value . "\t" . $purchase -> Free_Farmer_Quantity . "\t" . $purchase -> Free_Farmer_Value . "\t" . $total_system_quantity . "\t" . $total_system_value . "\t" . $total_captured_quantity . "\t" . $total_captured_value . "\t\n";
				}
				$system_total_value += $total_system_value;
				$system_total_quantity += $total_system_quantity;
				$captured_total_value += $total_captured_value;
				$captured_total_quantity += $total_captured_quantity;
				$batch_gross_total += $purchase -> Gross_Value;
				$batch_net_total += $purchase -> Net_Value;
				$farmer_reg_total += $purchase -> Farmer_Reg_Fee;
				$other_recovery_total += $purchase -> Other_Recoveries;
				$loan_recovery_total += $purchase -> Loan_Recovery;
				$quantity_total += $purchase -> Quantity;
				$batch_free_farmer_quantity += $purchase -> Free_Farmer_Quantity;
				$batch_free_farmer_value += $purchase -> Free_Farmer_Value;
			}

			if ($type == "pdf") {
				$data_buffer .= "<tr><td><b>Totals: </b></td><td>-</td><td>-</td><td>-</td><td>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($quantity_total) . "</b></td><td  class='amount'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($loan_recovery_total) . "</b></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($farmer_reg_total) . "</b></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($other_recovery_total) . "</b></td><td>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($batch_gross_total) . "</b></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($batch_net_total) . "</b></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($batch_free_farmer_quantity) . "</b></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($batch_free_farmer_value) . "</b></td></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($system_total_quantity) . "</b></td></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($system_total_value) . "</b></td></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($captured_total_quantity) . "</b></td></td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($captured_total_value) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals: \t-\t-\t-\t-\t" . $quantity_total . "\t-\t" . $loan_recovery_total . "\t" . $farmer_reg_total . "\t" . $other_recovery_total . "\t-\t" . $batch_gross_total . "\t" . $batch_net_total . "\t" . $batch_free_farmer_quantity . "\t" . $batch_free_farmer_value . "\t" . $system_total_quantity . "\t" . $system_total_value . "\t" . $captured_total_quantity . "\t" . $captured_total_value . "\t\n";
			}
		}
		if ($transaction_type == "agent_input_disbursements") {
			$batch_name = "Agent Input Disbursements Batch " . $batch_id;
			$total_inputs_value = 0;
			$disbursements = Agent_Input_Issue::getBatchDisbursements($batch_id);

			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>Agent</th><th>Delivery Note Number</th><th>Date</th><th>Farm Input</th><th>Quantity</th><th>Total Value</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Agent Input Disbursements, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "Agent\tDelivery Note Number\tDate\tFarm Input\tQuantity\tTotal Value\t\n";
			}
			foreach ($disbursements as $disbursement) {
				$formatted_date = date('d/m/Y', strtotime($disbursement -> Date));
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "</td><td class='center'>" . $disbursement -> Delivery_Note_Number . "</td><td class='center'>" . $formatted_date . "</td><td class='center'>" . $disbursement -> Farm_Input_Object -> Product_Name . "</td><td class='amount'>" . number_format($disbursement -> Quantity + 0) . "</td><td class='amount'>" . number_format($disbursement -> Total_Value + 0) . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "\t" . $disbursement -> Delivery_Note_Number . "\t" . $formatted_date . "\t" . $disbursement -> Farm_Input_Object -> Product_Name . "\t" . $disbursement -> Quantity . "\t" . $disbursement -> Total_Value . "\t\n";
				}
				$total_inputs_value += $disbursement -> Total_Value;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_inputs_value + 0) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals: \t-\t-\t-\t-\t" . $total_inputs_value . "\t\n";
			}

		}
		if ($transaction_type == "buying_center_receipts") {
			$batch_name = "Buying Center Receipts Batch " . $batch_id;
			$total_receipts_value = 0;
			$receipts = Buying_Center_Receipt::getBatchReceipts($batch_id);
			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>Buying Center</th><th>Receipt Number</th><th>Date</th><th>Amount</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Buying Center Receipts, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "Buying Center\tReceipt Number\tDate\tAmount\t\n";
			}
			foreach ($receipts as $receipt) {
				$formatted_date = date('d/m/Y', strtotime($receipt -> Date));
				if ($receipt -> Adjustment == "1") {
					$class = "(a)";
				} else {
					$class = "";
				}
				$total_transactions++;

				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $receipt -> Depot_Object -> Depot_Name . "</td><td class='center'>" . $receipt -> Receipt_Number . " " . $class . "</td><td class='center'>" . $formatted_date . "</td><td class='amount'>" . number_format($receipt -> Amount + 0) . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $receipt -> Depot_Object -> Depot_Name . "\t" . $receipt -> Receipt_Number . " " . $class . "\t" . $formatted_date . "\t" . $receipt -> Amount . "\t" . "\t\n";
				}
				$total_receipts_value += $receipt -> Amount;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_receipts_value + 0) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals:\t-\t-\t" . $total_receipts_value . "\t-\t\n";
			}
		}
		if ($transaction_type == "cash_receipts") {
			$batch_name = "Cash Receipts Batch " . $batch_id;
			$total_receipts_value = 0;
			$receipts = Cash_Receipt::getBatchReceipts($batch_id);

			if ($type == "pdf") {
				$data_buffer .= "<tr><th>Field Cashier</th><th>Receipt Number</th><th>Date</th><th>Amount</th></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Cash Receipts, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "Field Cashier\tReceipt Number\tDate\tAmount\t\n";
			}
			foreach ($receipts as $receipt) {
				$formatted_date = date('d/m/Y', strtotime($receipt -> Date));
				if ($receipt -> Adjustment == "1") {
					$class = "(a)";
				} else {
					$class = "";
				}
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<thead><tr><td>" . $receipt -> Field_Cashier_Object -> Field_Cashier_Name . "</td><td class='center'>" . $receipt -> Receipt_Number . " " . $class . "</td><td class='center'>" . $formatted_date . "</td><td class='amount'>" . number_format($receipt -> Amount + 0) . "</td></tr></thead>";
				} else if ($type == "excel") {
					$excel_buffer .= $receipt -> Field_Cashier_Object -> Field_Cashier_Name . "\t" . $receipt -> Receipt_Number . " " . $class . "\t" . $formatted_date . "\t" . $receipt -> Amount . "\t\n";
				}

				$total_receipts_value += $receipt -> Amount;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_receipts_value + 0) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals: \t-\t-\t" . $total_receipts_value . "\t-\t\n";
			}

		}
		if ($transaction_type == "cihc") {
			$batch_name = "CIH(c) Batch " . $batch_id;
			$total_disbursements_value = 0;
			$disbursements = Cash_Disbursement::getBatchDisbursements($batch_id);
			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>Field Cashier</th><th>CIH(c) Number</th><th>Date</th><th>Amount</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (CIH(c), " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "Field Cashier\tCIH(c) Number\tDate\tAmount\t\n";
			}

			foreach ($disbursements as $disbursement) {
				$formatted_date = date('d/m/Y', strtotime($disbursement -> Date));
				if ($disbursement -> Adjustment == "1") {
					$class = "(a)";
				} else {
					$class = "";
				}
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $disbursement -> Field_Cashier_Object -> Field_Cashier_Name . "</td><td class='center'>" . $disbursement -> CIH . " " . $class . "</td><td class='center'>" . $formatted_date . "</td><td class='amount'>" . number_format($disbursement -> Amount + 0) . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $disbursement -> Field_Cashier_Object -> Field_Cashier_Name . "\t" . $disbursement -> CIH . " " . $class . "\t" . $formatted_date . "\t" . number_format($disbursement -> Amount + 0) . "\t\n";
				}

				$total_disbursements_value += $disbursement -> Amount;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_disbursements_value) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals: \t-\t-\t" . $total_disbursements_value . "\t\n";
			}
		}
		if ($transaction_type == "cihb") {
			$batch_name = "CIH(b) Batch " . $batch_id;
			$total_disbursements_value = 0;
			$disbursements = Field_Cash_Disbursement::getBatchDisbursements($batch_id);

			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>Buying Center</th><th>Center Code</th><th>Field Cashier</th><th>CIH(b)</th><th>Receipt</th><th>Date</th><th>Amount</th><th>Details</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (CIH(b), " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "Buying Center\tCenter Code\tField Cashier\tCIH(b)\tReceipt\tDate\tAmount\tDetails\t\n";
			}
			foreach ($disbursements as $disbursement) {
				$formatted_date = date('d/m/Y', strtotime($disbursement -> Date));
				if ($disbursement -> Adjustment == "1") {
					$class = "(a)";
				} else {
					$class = "";
				}
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $disbursement -> Depot_Object -> Depot_Name . "</td><td class='center'>" . $disbursement -> Depot_Object -> Depot_Code . "</td><td class='center'>" . $disbursement -> Field_Cashier_Object -> Field_Cashier_Name . "</td><td class='center'>" . $disbursement -> CIH . " " . $class . "</td><td class='center'>" . $disbursement -> Receipt . "</td><td class='center'>" . $formatted_date . "</td><td class='amount'>" . number_format($disbursement -> Amount + 0) . "</td><td>" . $disbursement -> Details . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $disbursement -> Depot_Object -> Depot_Name . "\t" . $disbursement -> Depot_Object -> Depot_Code . "\t" . $disbursement -> Field_Cashier_Object -> Field_Cashier_Name . "\t" . $disbursement -> CIH . " " . $class . "\t" . $disbursement -> Receipt . "\t" . $formatted_date . "\t" . number_format($disbursement -> Amount + 0) . "\t" . $disbursement -> Details . "\t\n";
				}
				$total_disbursements_value += $disbursement -> Amount;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_disbursements_value + 0) . "</b></td><td class='center'>-</td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals: \t-\t-\t-\t-\t" . $total_disbursements_value . "\t-\t\n";
			}

		}
		if ($transaction_type == "input_transfers") {
			$batch_name = "Input Transfers Batch " . $batch_id;
			$total_inputs_value = 0;
			$disbursements = Region_Input_Issue::getBatchDisbursements($batch_id);

			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>Zone</th><th>Agent</th><th>Delivery Note Number</th><th>Date</th><th>Farm Input</th><th>Quantity</th><th>Total Value</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Input Transfers, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "Zone\tAgent\tDelivery Note Number\tDate\tFarm Input\tQuantity\tTotal Value\t\n";
			}
			foreach ($disbursements as $disbursement) {
				$formatted_date = date('d/m/Y', strtotime($disbursement -> Date));
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $disbursement -> Region_Object -> Region_Name . "</td><td>" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "</td><td class='center'>" . $disbursement -> Delivery_Note_Number . "</td><td class='center'>" . $formatted_date . "</td><td class='center'>" . $disbursement -> Farm_Input_Object -> Product_Name . "</td><td class='amount'>" . number_format($disbursement -> Quantity + 0) . "</td><td class='amount'>" . number_format($disbursement -> Total_Value + 0) . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $disbursement -> Region_Object -> Region_Name . "\t" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "\t" . $disbursement -> Delivery_Note_Number . "\t" . $formatted_date . "\t" . $disbursement -> Farm_Input_Object -> Product_Name . "\t" . $disbursement -> Quantity . "\t" . $disbursement -> Total_Value . "\t\n";
				}

				$total_inputs_value += $disbursement -> Total_Value;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_inputs_value + 0) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals: \t-\t-\t-\t-\t-\t" . $total_inputs_value . "\t\n";
			}

		}
		if ($transaction_type == "mopping_payments") {
			$batch_name = "Mopping Payments Batch " . $batch_id;
			$total_amount = 0;
			$payments = Mopping_Payment::getBatchPayments($batch_id);

			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>BC Name</th><th>BC Code</th><th>Voucher Number</th><th>Date</th><th>Amount</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Mopping Payments, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "BC Name\tBC Code\tVoucher Number\tDate\tAmount\t\n";
			}
			foreach ($payments as $payment) {
				$formatted_date = date('d/m/Y', strtotime($payment -> Date));
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $payment -> Depot_Object -> Depot_Name . "</td><td class='center'>" . $payment -> Depot_Object -> Depot_Code . "</td><td class='center'>" . $payment -> Voucher_Number . "</td><td class='center'> " . $formatted_date . "</td><td class='amount'>" . number_format($payment -> Amount + 0) . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $payment -> Depot_Object -> Depot_Name . "\t" . $payment -> Depot_Object -> Depot_Code . "\t" . $payment -> Voucher_Number . "\t" . "\t" . $formatted_date . "\t" . $payment -> Amount . "\t\n";
				}

				$total_amount += $payment -> Amount;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_amount + 0) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals:\t-\t-\t-\t" . $total_amount . "\t\n";
			}

		}
		if ($transaction_type == "loan_recovery_receipts") {
			$batch_name = "Loan Recovery Receipts Batch ".$batch_id;
			$total_receipts_value = 0;
			$receipts = Loan_Recovery_Receipt::getBatchReceipts($batch_id);
			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>FBG</th><th>Receipt Number</th><th>Date</th><th>Received From</th><th>Amount</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Loan Recovery Receipts, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "FBG\tReceipt Number\tDate\tReceived From\tAmount\t\n";
			}

			foreach ($receipts as $receipt) {
				$formatted_date = date('d/m/Y', strtotime($receipt -> Date));
				if ($receipt -> Adjustment == "1") {
					$class = "(a)";
				} else {
					$class = "";
				}
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $receipt -> FBG_Object -> Group_Name . "</td><td class='center'>" . $receipt -> Receipt_Number . " " . $class . "</td><td class='center'>" . $formatted_date . "</td><td class='center'>" . $receipt -> Received_From . "</td><td class='amount'>" . number_format($receipt -> Amount + 0) . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $receipt -> FBG_Object -> Group_Name . "\t" . $receipt -> Receipt_Number . " " . $class . "\t" . $formatted_date . "\t" . $receipt -> Received_From . "\t" . $receipt -> Amount .  "\t\n";
				}

				$total_receipts_value += $receipt -> Amount;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr><td><span><b>Totals: </b></td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td style='border-top:2px solid black;' class='amount'><b>" . number_format($total_receipts_value + 0) . "</b></td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals:\t-\t-\t-\t" . $total_receipts_value . "\t\n";
			}
		}
		if ($transaction_type == "buying_center_summaries") {
			$page_layout = "A4-L";
			$batch_name = "Buying Center Summaries Batch ".$batch_id;
			$total_bags_closing = 0;
			$total_stock_closing = 0;
			$total_cash_closing = 0;
			$total_purchase_quantity = 0;
			$total_purchase_value = 0;
			$total_deductions = 0;
			$total_deliveries = 0;
			$summaries = Buying_Center_Summary::getBatchSummaries($batch_id);
			if ($type == "pdf") {
				$data_buffer .= "<thead><tr><th>BC Name</th><th>Summary Number</th><th>Date</th><th>Bags B/F</th><th>Stocks B/F</th><th>Cash B/F</th><th>Bags Received</th><th>Cash Received</th><th>PPV Start</th><th>PPV End</th><th>Purchase Kgs.</th><th>Purchase Tsh.</th><th>Input Deductions</th><th>Cotton Deliveries</th><th>Delivery Note</th><th>Bags C/F</th><th>Stocks C/F</th><th>Cash C/F</th><th>Prepared By</th></tr></thead>";
			} else if ($type == "excel") {
				$excel_buffer .= "Batch Details: (Buying Center Summaries, " . $batch_id . ")\tEntered By: " . $batch -> User_Object -> Name . "\t\n\n";
				$excel_buffer .= "BC Name\tSummary Number\tDate\tBags B/F\tStocks B/F\tCash B/F\tBags Received\tCash Received\tPPV Start\tPPV End\tPurchase Kgs.\tPurchase Tsh.\tInput Deductions\tCotton Deliveries\tDelivery Note\tBags C/F\tStocks C/F\tCash C/F\tPrepared By\t\n";
			}

			foreach ($summaries as $summary) {
				$formatted_date = date('d/m/Y', strtotime($summary -> Date));
				if ($summary -> Adjustment == "1") {
					$class = "(a)";
				} else {
					$class = "";
				}
				$total_transactions++;
				if ($type == "pdf") {
					$data_buffer .= "<tr><td>" . $summary -> Depot_Object -> Depot_Name . "</td><td class='center'>" . $summary -> Summary_Number . "</td><td class='center'>" . $formatted_date . "</td><td class='amount'>" . $summary -> Opening_Bags . "</td><td class='amount'>" . $summary -> Opening_Stocks . "</td><td class='amount'>" . $summary -> Opening_Cash . "</td><td class='amount'>" . $summary -> Bags_Received . "</td><td class='amount'>" . $summary -> Cash_Received . "</td><td class='center'>" . $summary -> Start_Ppv . "</td><td class='center'>" . $summary -> End_Ppv . "</td><td class='amount'>" . $summary -> Purchase_Quantity . "</td><td class='amount'>" . $summary -> Purchase_Value . "</td><td class='amount'>" . $summary -> Input_Deductions . "</td><td class='center'>" . $summary -> Cotton_Deliveries . "</td><td class='amount'>" . $summary -> Delivery_Note . "</td><td class='amount'>" . $summary -> Closing_Bags . "</td><td class='amount'>" . $summary -> Closing_Stock . "</td><td class='amount'>" . $summary -> Closing_Cash . "</td><td>" . $summary -> Prepared_By . "</td></tr>";
				} else if ($type == "excel") {
					$excel_buffer .= $summary -> Depot_Object -> Depot_Name . "\t" . $summary -> Summary_Number . "\t" . $formatted_date . "\t" . $summary -> Opening_Bags . "\t" . $summary -> Opening_Stocks . "\t" . $summary -> Opening_Cash . "\t" . $summary -> Bags_Received . "\t" . $summary -> Cash_Received . "\t" . $summary -> Start_Ppv . "\t" . $summary -> End_Ppv . "\t" . $summary -> Purchase_Quantity . "\t" . $summary -> Purchase_Value . "\t" . $summary -> Input_Deductions . "\t" . $summary -> Cotton_Deliveries . "\t" . $summary -> Delivery_Note . "\t" . $summary -> Closing_Bags . "\t" . $summary -> Closing_Stock . "\t" . $summary -> Closing_Cash . "\t" . $summary -> Prepared_By . "\t\n";
				}

				$total_bags_closing += $summary -> Closing_Bags;
				$total_stock_closing += $summary -> Closing_Stock;
				$total_cash_closing += $summary -> Closing_Cash;
				$total_purchase_quantity += $summary -> Purchase_Quantity;
				$total_purchase_value += $summary -> Purchase_Value;
				$total_deductions += $summary -> Input_Deductions;
				$total_deliveries += $summary -> Cotton_Deliveries;
			}
			if ($type == "pdf") {
				$data_buffer .= "<tr>Totals:<td>-</td><td>-</td><td class='amount'>-</td><td class='amount'>-</td><td class='amount'>-</td><td class='amount'>-</td><td class='amount'>-</td><td class='center'>-</td><td class='center'>-</td><td class='amount'>" . $total_purchase_quantity . "</td><td class='amount'>" . $total_purchase_value . "</td><td class='amount'>" . $total_deductions . "</td><td class='amount'>" . $total_deliveries . "</td><td class='center'>-</td><td class='amount'>" . $total_bags_closing . "</td><td class='amount'>" . $total_stock_closing . "</td><td class='amount'>" . $total_cash_closing . "</td><td>-</td></tr>";
			} else if ($type == "excel") {
				$excel_buffer .= "\nTotals:\t-\t-\t-\t-\t-\t-\t-\t-\t-\t" . $total_purchase_quantity . "\t" . $total_purchase_value . "\t" . $total_deductions . "\t" . $total_deliveries . "\t-\t" . $total_bags_closing . "\t" . $total_stock_closing . "\t" . $total_cash_closing . "\t-\t\n";
			}
		}
		$data_buffer .= "</table>";
		//echo $data_buffer;

		if ($type == "pdf") {
			$this -> generatePDF($data_buffer, $batch, $total_transactions, $page_layout, $batch_name);
		} else if ($type == "excel") {
			header("Content-type: application/vnd.ms-excel; name='excel'");
			header("Content-Disposition: filename=" . $batch_name . ".xls");
			// Fix for crappy IE bug in download.
			header("Pragma: ");
			header("Cache-Control: ");
			echo $excel_buffer;
		}

	}

	function generatePDF($data, $batch, $total_transactions = 0, $type, $batch_name) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline;margin-top:-50px;'>Batch Details (" . $batch -> Transaction_Type_Object -> Name . "," . $batch -> id . ") </h3>";
		$html_title .= "<h5 style='text-align:center;'> entered by: " . $batch -> User_Object -> Name . " (" . $total_transactions . " transactions)</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', $type);
		$this -> mpdf -> SetTitle($batch_name);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$footer = 'Generated on: {DATE d/m/Y}|-{PAGENO}-| ' . $batch_name;
		$this -> mpdf -> SetFooter($footer);
		/* defines footer for Odd and Even Pages - placed at Outer margin */
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$report_name = $batch_name . ".pdf";
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
