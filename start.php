<?php

Autoloader::map(array(
	'Hybrid_Auth' => Bundle::path('hybridauth').'hybridauth/Hybrid/Auth.php',
	'Hybrid_Endpoint' => Bundle::path('hybridauth').'hybridauth/Hybrid/Endpoint.php'
));

Laravel\IoC::singleton('hybridauth', function() {
	return new Hybrid_Auth(path('app') . '/config/hybridauth.php');
});
