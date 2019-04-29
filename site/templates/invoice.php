<?php
	ini_set('max_execution_time', 240);
	header('Content-Type: application/json');

	$response = array(
		'start' => date('m/d/Y H:i A'),
		'response' => '',
		'end' => ''
	);

	if ($input->get->text('apikey') != $config->accesskey) {
		$response = array(
			'error'    => true,
			'messsage' => 'You do not have have access to this function.'
		);
	} else {
		$endpoint = $sap->create_endpoint("invoice");
		$action = $input->get->text('action');

		switch ($action) {
			case 'get-invoice':
				$invoicenbr = $input->get->text('invnbr');
				$response['response'] = $endpoint->get_invoice($invoicenbr);
				break;
			case 'get-invoices-after':
				// If date is today or empty, then date is 2 days before today
				if (empty($input->get->date) || $input->get->text('date') == 'today') {
					$date = date('Y-m-d', strtotime('-2 days'));
				} else {
					$date = $input->get->text('date');
				}
				$response['response'] = $endpoint->get_invoices_modified_after($date);
				break;
			case 'get-invoices-before':
				// If date is today or empty, then date is 2 days before today
				if (empty($input->get->date) || $input->get->text('date') == 'today') {
					$date = date('Y-m-d', strtotime('-2 days'));
				} else {
					$date = $input->get->text('date');
				}
				$response['response'] = $endpoint->get_invoices_modified_before($date);
				break;
		}
		$response['end'] = date('m/d/Y H:i A');
	}
	echo json_encode($response);
