<?php
class Tonnage_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function get_weighted_average_graph_data() {
		echo '<chart caption="Average Tonnage" xAxisName="Month" yAxisName="Tonnes" showValues="0" decimals="0" formatNumberScale="0">
<set label="Jan" value="462"/>
<set label="Feb" value="857"/>
<set label="Mar" value="671"/>
<set label="Apr" value="494"/>
<set label="May" value="761"/>
<set label="Jun" value="960"/>
<set label="Jul" value="629"/>
<set label="Aug" value="622"/>
<set label="Sep" value="376"/>
<set label="Oct" value="494"/>
<set label="Nov" value="761"/>
<set label="Dec" value="960"/>
</chart>';
	}

}
