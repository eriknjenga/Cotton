<?php
class Activity_Graph extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function getDailyTrend($graphs, $from = "", $to = "") {
		if ($from == "") {
			$from = date('d-m-Y', strtotime('-7 days', date('U')));
		}
		if ($to == "") {
			$to = date('d-m-Y');
		}
		$label_from = date('M-d-Y', strtotime($from));
		$label_to = date('M-d-Y', strtotime($to));
		$loop_from = $from;
		$loop_to = $to;
		$graphs_array = explode('-', $graphs);
		$this -> load -> database();
		//Save the daily data points
		$days = array();
		//Start generating the graph
		$chart = '<chart caption="Daily Activity" connectNullData="1" lineDashGap="6" yAxisMinValue="0" yAxisMaxValue="1000000" numDivLines="10" labelDisplay="Rotate" slantLabels="1" subcaption="From ' . $label_from . ' to ' . $label_to . '" xAxisName="Day" yAxisName="Kgs" showValues="0" showBorder="0" showAlternateHGridColor="0" divLineAlpha="10"  bgColor="FFFFFF"  exportEnabled="1" exportHandler="' . base_url() . 'Scripts/FusionCharts/ExportHandlers/PHP/FCExporter.php" exportAtClient="0" exportAction="download">';
		//Generate the categories i.e. the dates
		$chart .= '<categories>';
		$counter = 0;
		while (strtotime($loop_from) <= strtotime($loop_to)) {
			$days[$counter] = date('M-d', strtotime($loop_from));
			$chart .= '<category label="' . date('M-d', strtotime($loop_from)) . '"/>';
			$loop_from = date("d-m-Y", strtotime("+1 day", strtotime($loop_from)));
			$counter++;
		}
		$chart .= '</categories>';
		foreach ($graphs_array as $graph) {
			if ($graph == "purchases") {
				$sql = "SELECT sum(quantity+free_farmer_quantity) as total,date FROM `purchase` p where batch_status = '2'  and str_to_date(p.date,'%m/%d/%Y') between str_to_date('" . $from . "','%d-%m-%Y') and str_to_date('" . $to . "','%d-%m-%Y') group by str_to_date(p.date,'%m/%d/%Y') order by str_to_date(p.date,'%m/%d/%Y') asc";
				$query = $this -> db -> query($sql);
				$production_data = $query -> result_array();
				$test = array_combine($days, $production_data);
				$chart .= '<dataset seriesName="Purchases">';
				//make the date the key of the array
				$values_array = array();
				foreach ($production_data as $data) {
					$date = date('M-d', strtotime($data['date']));
					$values_array[$date] = $data['total'];
				}
				$counter = 0;  
				foreach ($days as $day) { 
					//if a value exists, continue
					if (isset($values_array[$day])) {
						//check if the next value is non-existent, if so, display a dotted line, else, display a kawaida line
						if (sizeof($values_array) != $counter) {
							if (isset($values_array[$days[$counter + 1]])) {
								 
								$chart .= '<set value="' . $values_array[$day] . '"/>';
							} else {
								$chart .= '<set value="' . $values_array[$day] . '" dashed="1"/>';
							}
						}

					} else {
						$chart .= '<set  />';
					}
					$counter++;
				}
				$chart .= '</dataset>';
			}
			if ($graph == "dispatches") {
				$sql = "SELECT sum(net_weight) as total,transaction_date as date FROM `weighbridge` w where weighing_type = '2'  and str_to_date(transaction_date,'%d/%m/%Y') between str_to_date('" . $from . "','%d-%m-%Y') and str_to_date('" . $to . "','%d-%m-%Y') group by str_to_date(transaction_date,'%d/%m/%Y') order by str_to_date(transaction_date,'%d/%m/%Y') asc";
				$query = $this -> db -> query($sql);
				$production_data = $query -> result_array(); 
				$chart .= '<dataset seriesName="Dispatches">';
				//make the date the key of the array
				$values_array = array();
				foreach ($production_data as $data) {
					$data['date'] = str_replace("/", "-", $data['date']);
					$date = date('M-d', strtotime($data['date']));
					$values_array[$date] = $data['total'];
				}
				$counter = 0;  
				foreach ($days as $day) { 
					//if a value exists, continue
					if (isset($values_array[$day])) {
						//check if the next value is non-existent, if so, display a dotted line, else, display a kawaida line
						if (sizeof($values_array) != $counter) {
							if (isset($values_array[$days[$counter + 1]])) {
								 
								$chart .= '<set value="' . $values_array[$day] . '"/>';
							} else {
								$chart .= '<set value="' . $values_array[$day] . '" dashed="1"/>';
							}
						}

					} else {
						$chart .= '<set  />';
					}
					$counter++;
				}
				$chart .= '</dataset>';
			}
			if ($graph == "seeds") {

				$sql = "SELECT sum(net_weight) as total,transaction_date as date FROM `weighbridge` w where weighing_type = '3'  and str_to_date(transaction_date,'%d/%m/%Y') between str_to_date('" . $from . "','%d-%m-%Y') and str_to_date('" . $to . "','%d-%m-%Y') group by str_to_date(transaction_date,'%d/%m/%Y') order by str_to_date(transaction_date,'%d/%m/%Y') asc";
				$query = $this -> db -> query($sql);
				$production_data = $query -> result_array();
				$chart .= '<dataset seriesName="Seeds Out">';
				//make the date the key of the array
				$values_array = array();
				foreach ($production_data as $data) {
					$data['date'] = str_replace("/", "-", $data['date']);
					$date = date('M-d', strtotime($data['date']));
					$values_array[$date] = $data['total'];
				}
				$counter = 0;  
				foreach ($days as $day) { 
					//if a value exists, continue
					if (isset($values_array[$day])) {
						//check if the next value is non-existent, if so, display a dotted line, else, display a kawaida line
						if (sizeof($values_array) != $counter) {
							if (isset($values_array[$days[$counter + 1]])) {
								 
								$chart .= '<set value="' . $values_array[$day] . '"/>';
							} else {
								$chart .= '<set value="' . $values_array[$day] . '" dashed="1"/>';
							}
						}

					} else {
						$chart .= '<set  />';
					}
					$counter++;
				}
				$chart .= '</dataset>';
			}
			if ($graph == "sg") {
				$sql = "SELECT sum(gross_weight) as total,date FROM `production_data` p where ginnery = '1'  and str_to_date(p.date,'%m/%d/%Y') between str_to_date('" . $from . "','%d-%m-%Y') and str_to_date('" . $to . "','%d-%m-%Y') group by str_to_date(p.date,'%m/%d/%Y') order by str_to_date(p.date,'%m/%d/%Y') asc";
				$query = $this -> db -> query($sql);
				$production_data = $query -> result_array();
				$chart .= '<dataset seriesName="Sow Gin">';
				//make the date the key of the array
				$values_array = array();
				foreach ($production_data as $data) {
					$date = date('M-d', strtotime($data['date']));
					$values_array[$date] = $data['total'];
				}
				$counter = 0;  
				foreach ($days as $day) { 
					//if a value exists, continue
					if (isset($values_array[$day])) {
						//check if the next value is non-existent, if so, display a dotted line, else, display a kawaida line
						if (sizeof($values_array) != $counter) {
							if (isset($values_array[$days[$counter + 1]])) {
								 
								$chart .= '<set value="' . $values_array[$day] . '"/>';
							} else {
								$chart .= '<set value="' . $values_array[$day] . '" dashed="1"/>';
							}
						}

					} else {
						$chart .= '<set  />';
					}
					$counter++;
				}
				$chart .= '</dataset>';
			}
			if ($graph == "rg") {

				$sql = "SELECT sum(gross_weight) as total,date FROM `production_data` p where ginnery = '2'  and str_to_date(p.date,'%m/%d/%Y') between str_to_date('" . $from . "','%d-%m-%Y') and str_to_date('" . $to . "','%d-%m-%Y') group by str_to_date(p.date,'%m/%d/%Y') order by str_to_date(p.date,'%m/%d/%Y') asc";
				$query = $this -> db -> query($sql);
				$production_data = $query -> result_array();
				$chart .= '<dataset seriesName="Roller Gin">';
				//make the date the key of the array
				$values_array = array();
				foreach ($production_data as $data) {
					$date = date('M-d', strtotime($data['date']));
					$values_array[$date] = $data['total'];
				}
				$counter = 0;  
				foreach ($days as $day) { 
					//if a value exists, continue
					if (isset($values_array[$day])) {
						//check if the next value is non-existent, if so, display a dotted line, else, display a kawaida line
						if (sizeof($values_array) != $counter) {
							if (isset($values_array[$days[$counter + 1]])) {
								 
								$chart .= '<set value="' . $values_array[$day] . '"/>';
							} else {
								$chart .= '<set value="' . $values_array[$day] . '" dashed="1"/>';
							}
						}

					} else {
						$chart .= '<set  />';
					}
					$counter++;
				}
				$chart .= '</dataset>';
			}
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
		/*$chart = '<chart connectNullData="1" caption="Daily Visits" subcaption="(from 8/6/2006 to 8/12/2006)" lineThickness="1" showValues="0" formatNumberScale="0" anchorRadius="2" divLineAlpha="20" divLineColor="CC3300" divLineIsDashed="1" showAlternateHGridColor="1" alternateHGridAlpha="5" alternateHGridColor="CC3300" shadowAlpha="40" labelStep="2" numvdivlines="5" chartRightMargin="35" bgColor="FFFFFF,CC3300" bgAngle="270" bgAlpha="10,10">
		 <categories>
		 <category label="8/6/2006"/>
		 <category label="8/7/2006"/>
		 <category label="8/8/2006"/>
		 <category label="8/9/2006"/>
		 <category label="8/10/2006"/>
		 <category label="8/11/2006"/>
		 <category label="8/12/2006"/>
		 </categories>
		 <dataset seriesName="Offline Marketing" color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1">
		 <set value="1327"/>
		 <set value="1826"/>
		 <set value="1699"/>
		 <set value="1511"/>
		 <set value="1904"/>
		 <set value="1957"/>
		 <set value="1296"/>
		 </dataset>
		 <dataset seriesName="Search" color="F1683C" anchorBorderColor="F1683C" anchorBgColor="F1683C">
		 <set value="2042"/>
		 <set value="3210"/>
		 <set value="2994"/>
		 <set value="3115"/>
		 <set value="2844"/>
		 <set value="3576"/>
		 <set value="1862"/>
		 </dataset>
		 <dataset seriesName="Paid Search" color="2AD62A" anchorBorderColor="2AD62A" anchorBgColor="2AD62A">
		 <set value="850"/>
		 <set value="1010"/>
		 <set value="1116"/>
		 <set value="1234"/>
		 <set value="1210"/>
		 <set value="1054"/>
		 <set value="802"/>
		 </dataset>
		 <dataset seriesName="From Mail" color="DBDC25" anchorBorderColor="DBDC25" anchorBgColor="DBDC25">
		 <set value="541"/>
		 <set value="781"/>
		 <set value="920"/>
		 <set value="754"/>
		 <set value="840"/>
		 <set  />
		 <set value="451"/>
		 </dataset>
		 <styles>
		 <definition>
		 <style name="CaptionFont" type="font" size="12"/>
		 </definition>
		 <application>
		 <apply toObject="CAPTION" styles="CaptionFont"/>
		 <apply toObject="SUBCAPTION" styles="CaptionFont"/>
		 </application>
		 </styles>
		 </chart>';*/
		echo $chart;
	}

	public function base_params($data) {
		$data['title'] = "Weighbridge Management";
		$data['content_view'] = "production_v";
		$data['quick_link'] = "add_account";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);
	}

}
