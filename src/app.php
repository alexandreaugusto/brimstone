<?php

define('FACEBOOK_API_KEY',    '950121685075129');
define('FACEBOOK_API_SECRET', '01656c84aafa4d72f85d270b454a130e');
define('TWITTER_API_KEY',     '');
define('TWITTER_API_SECRET',  '');
define('GOOGLE_API_KEY',      '');
define('GOOGLE_API_SECRET',   '');
define('GITHUB_API_KEY',      '');
define('GITHUB_API_SECRET',   '');

$app['debug'] = true;
$app['charset'] = "iso-8859-1";

$app->register(new Gigablah\Silex\OAuth\OAuthServiceProvider(), array(
    'oauth.services' => array(
        'Facebook' => array(
            'key' => FACEBOOK_API_KEY,
            'secret' => FACEBOOK_API_SECRET,
            'scope' => array('email'),
            'user_endpoint' => 'https://graph.facebook.com/me'
        )
    )
));

$app->register(new DerAlex\Silex\YamlConfigServiceProvider('config.yml'));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array (
        'mysql' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'genericregister',
            'user'      => 'root',
            'password'  => 'root@itaguare',
        ),
    ),
));

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
        'security.firewalls' => array(
            'login_path' => array(
                'pattern' => '^/login$',
                'anonymous' => true
            ),
            'register_path' => array(
                'pattern' => '^/register-user$',
                'anonymous' => true
            ),
            'default' => array(
                'pattern' => '^/.*$',
                'anonymous' => false,
                'form' => array(
                    'login_path' => '/login',
                    'check_path' => '/admin/login_check',
                ),
                'logout' => array(
                    'logout_path' => '/admin/logout',
                    'invalidate_session' => false
                ),
                'users' => $app->share(function($app) { 
                    return new App\User\UserProvider($app['dbs']['mysql']); 
                }),
            )
        ),
        'security.access_rules' => array(
            array('^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/register-user$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/.*$', 'ROLE_USER')
        )
    ));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\FormServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
	'locale' => 'sr_Latn',
    'translator.domains' => array(),
));

return $app;