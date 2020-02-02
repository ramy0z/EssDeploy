
<?php 
	/*Security*/
	define('SECRETE_KEY', '!@#$%^&*(MY_SECRET_KEY_AND_IT_VERY_STRONG_!@#$%^&*(');
	define('ISSUER_CLAIM', 'THE_ISSUER servername');
	define('AUDIENCE_CLAIM_CLAIM', 'THE_AUDIENCE');
	// $_SERVER['DOCUMENT_ROOT']
	define('BASE_URL', 'http://localhost/Ess/api');
	define('IMAGE_BASE_URL', 'http://localhost/Ess/api/uploads/users'); //$_SERVER['DOCUMENT_ROOT'] . "/ESS/api";
	define('SITE_URL', 'http://www.ess.com');
	
	/*Data Type*/
	define('BOOLEAN', 	'1');
	define('INTEGER', 	'2');
	define('STRING', 	'3');
	define('EMAIL', 	'4');
	/*Error Codes*/
	define('REQUEST_METHOD_NOT_VALID',		        100);
	define('REQUEST_CONTENTTYPE_NOT_VALID',	        101);
	define('REQUEST_NOT_VALID', 			        102);
    define('VALIDATE_PARAMETER_REQUIRED', 			103);
	define('VALIDATE_PARAMETER_DATATYPE', 			104);
	define('API_NAME_REQUIRED', 					105);
	define('API_PARAM_REQUIRED', 					106);
	define('API_DOST_NOT_EXIST', 					107);
	define('INVALID_USER_PASS', 					108);
	define('USER_NOT_ACTIVE', 						109);
	define('FAILD_RESPONSE', 						110);
	define('SUCCESS_RESPONSE', 						200);
	/*Server Errors*/
	define('JWT_PROCESSING_ERROR',					300);
	define('ATHORIZATION_HEADER_NOT_FOUND',			301);
	define('ACCESS_TOKEN_ERRORS',					302);
	define('REQUEST_METHOD_NOT_FOUND',		        400);
	define('CATCH_DB_ERROR',				        401);
?>