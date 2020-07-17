<html>
	<head>
		<title>Telnyx Docs Test</title>
	</head>
	<body>
		<h1>Telnyx Docs</h1>
		<?php
			require_once __DIR__.'/vendor/autoload.php';

			\Telnyx\Telnyx::setApiKey('YOUR_API_KEY ');
			
			$json = json_decode(file_get_contents("php://input"), true);
			if($json){
				print_r($json);
				$record_type = $json['data']['record_type'];
				if($record_type == "event") {
					$call_control_id = $json['data']['payload']['call_control_id'];
					$event_type = $json['data']['event_type'];

					if($event_type == "call.initiated") {
						//NEEDS WORK HERE TO CREATE CALL AND ANSWER (Currently does not work)
						$call = \Telnyx\Call::Create($call_control_id);
						$result = $call.answer();
					} elseif($event_type == "call.answered") {
						//Add call to array of calls
						// If it is the first call create the conference
						// If it is not first, add it to the conference
					}
				}
			}
		?>
	</body>
</html>