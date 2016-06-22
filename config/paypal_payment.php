<?php

return array(
	# Account credentials from developer portal
	'Account' => array(
		'ClientId' => 'Ab2zY8aous0kcCJhdj5UR1uwQk06JkpWqr6jorEtuvPSdorHFcpPnYw8I58FLI1iBln_5wzwdLPSSZ2B',
		'ClientSecret' => 'EJhLlOYRFtFlSI-GKWilKIUdvLTOpOb9JIxsWCq6kq9v-5tX2yzgeyGReadXqK1wsw6s2FPDUd0pBncR',
	),

	# Connection Information
	'Http' => array(
		// 'ConnectionTimeOut' => 30,
		'Retry' => 1,
		//'Proxy' => 'http://[username:password]@hostname[:port][/path]',
	),

	# Service Configuration
	'Service' => array(
		# For integrating with the live endpoint,
		# change the URL to https://api.paypal.com!
		'EndPoint' => 'https://api.sandbox.paypal.com',
	),


	# Logging Information
	'Log' => array(
		'LogEnabled' => true,

		# When using a relative path, the log file is created
		# relative to the .php file that is the entry point
		# for this request. You can also provide an absolute
		# path here
		'FileName' => '../PayPal.log',

		# Logging level can be one of FINE, INFO, WARN or ERROR
		# Logging is most verbose in the 'FINE' level and
		# decreases as you proceed towards ERROR
		'LogLevel' => 'FINE',
	),
);
