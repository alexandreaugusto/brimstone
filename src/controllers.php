<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Security\Core\User\User as AdvancedUser;

$app->before(function () use ($app) {
    $app['translator']->addLoader('xlf', new Symfony\Component\Translation\Loader\XliffFileLoader());
    $app['translator']->addResource('xlf', __DIR__.'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators/validators.sr_Latn.xlf', 'sr_Latn', 'validators');
});

$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.html', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->get('/', function() use ($app) {
    $token = $app['security']->getToken();
    $email = $token->getUser()->getUsername();

    $sql = "SELECT nome, email FROM users WHERE email = ?";
    $data = $app['dbs']['mysql']->fetchAssoc($sql, array($email));

    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('nome')
        ->add('email', 'email')
        ->add('endereco', 'text')
        ->add('website', 'url')
        ->getForm();

        return $app['twig']->render('form.html', array('form' => $form->createView(), 'msg' => "", 'header' => "Adicione informacoes"));
});

$app->match('/register-user', function (Request $request) use ($app) {

    $form = $app['form.factory']->createBuilder('form')
        ->add('nome')
        ->add('email', 'email')
        ->add('senha', 'password')
        ->getForm();

    $message = "";

    if ($request->isMethod('POST')) {
        $form->bind($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $user = new AdvancedUser($data['email'], $data['senha']);
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            $encodedPassword = $encoder->encodePassword($data['senha'], $user->getSalt());
            $app['dbs']['mysql']->insert('users', array(
                'email' => $data['email'], 'senha' => $encodedPassword,
                'nome' => $data['nome']));
            $message = 'Usuario cadastrado!';
        }
    }

    // display the form
    return $app['twig']->render('form.html', array('form' => $form->createView(), 'msg' => $message, 'header' => ""));
});
