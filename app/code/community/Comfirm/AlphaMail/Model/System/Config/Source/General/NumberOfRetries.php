<?php

/* Determines how we should handle disaster moments (in case the primary AM servers cannot be reached) */

class Comfirm_AlphaMail_Model_System_Config_Source_General_NumberOfRetries
{
	public function toOptionArray()
	{
		$result = array();

		$result[0] = "No retry, fallback directly";

		for($i=1;$i<10;++$i){
			$result[$i] = $i . " " . ($i == 1 ? "retry" : "retries");
		}

		return $result;
	}
}

?>