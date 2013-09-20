# HybridAuth bundle for Laravel 3

Note: Laravel 4 users can install HybridAuth using Composer. This bundle was created to simplify things for legacy Laravel 3 projects.

## Installation

Install the bundle using Artisan CLI:

```
php artisan bundle:install hybridauth
```

Add the following to `application/bundles.php`:

```php
'hybridauth' => array('auto' => true),
```

Then you can use HybridAuth in your project.

## Usage

First create an appropriate HybridAuth config file in `application/config/hybridauth.php`, example:

```php
return
	array(
		"base_url" => URL::to('hybrid/endpoint'),

		"providers" => array (
			"Facebook" => array (
				"enabled" => true,
				"keys"    => array ( "id" => "123456", "secret" => "78910" ),
				"scope" => 'email'
			),

			"LinkedIn" => array (
				"enabled" => true,
				"keys"    => array ( "key" => "123456", "secret" => "78910" ),
				"scope" => 'r_basicprofile, r_emailaddress'
			)
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);
```

For more information about this configuration file see [HybridAuth Configuration Guide](http://hybridauth.sourceforge.net/userguide/Configuration.html).

Note that `base_url` points to a route providing a HybridAuth Endpoint. You need to provide such a route for authentication to work correctly.

In the example controller below we provide a HybridAuth Endpoint and show a basic usage example for the HybridAuth IoC container.

```php
class Hybrid_Controller extends Base_Controller {
	/**
	 * Attempts to log a user in and prints his profile information on screen
	 */
	public function action_auth() {
		$provider = Input::has('provider') ? Input::get('provider') : 'facebook';
		try {
			$hybrid = IoC::resolve('hybridauth');
			$auth = $hybrid->authenticate($provider);
			$profile = $auth->getUserProfile();
		} catch (Exception $e) {
			return $e->getMessage();
		}

		echo "Connected with: <b>{$auth->id}</b><br />";
		echo "As: <b>{$profile->displayName}</b><br />";
		echo "<pre>" . print_r( $profile, true ) . "</pre><br />";
	}

	/**
	 * Provides a HybridAuth endpoint
	 */
	public function action_endpoint() {
		try {
			Hybrid_Endpoint::process();
		} catch (Exception $e) {
			return Redirect::to('hybrid/auth');
		}
		return;
	}
}
```

Then you can register such a controller in your `routes.php`:

```php
Route::controller('hybrid');
```

Sample URLs you can use in your app to log users in using various networks:

```php
URL::to('hybrid/auth?provider=facebook')
URL::to('hybrid/auth?provider=google')
URL::to('hybrid/auth?provider=twitter')
```

If you wonder how you could integrate it with built-in authorization, here's a hint:

```php
	$user = User::where('hybridId', $profile->identifier)->first();
	if ($user) {
		Auth::login($user);
	}
```

## Further reading

For more information about HybridAuth and the API available via $hybrid instance (of Hybrid_Auth class), visit [HybridAuth website](http://hybridauth.sourceforge.net/).

If a new version of HybridAuth is released, feel free to update the files inside `bundles/hybridauth/hybridauth` folder.

If you need to use some additional or third-party providers, copy provider classes to `bundles/hybridauth/hybridauth/Hybrid/Providers`.
