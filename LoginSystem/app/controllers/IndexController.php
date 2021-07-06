<?php
declare(strict_types=1);

use Phalcon\Mvc\User\Component;
use Phalcon\Security;
use Phalcon\Escaper;
use Phalcon\Flash\Direct;

class IndexController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function loginAction()
    {
        if ($this->session->has('AUTH_NAME') AND $this->session->has('AUTH_EMAIL') AND $this->session->has('AUTH_ROLE')) {
            return $this->response->redirect('index/index');
        } else {
            $security = new Security();

            if ($this->request->isPost()){
                $email    = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $user = Users::findFirst([
                    'email = :email:',
                    'bind' => [
                        'email' => $email,
                    ]
                ]);

                if ($user){
                    if ($this->security->checkHash($password, $user->password)) {
                        $this->session->set('AUTH_ID', $user->id);
                        $this->session->set('AUTH_NAME', $user->name);
                        $this->session->set('AUTH_EMAIL', $user->email);
                        $this->session->set('AUTH_ROLE', $user->role);

                        $this->flashSession->success('Success');
                        return $this->response->redirect('index/index');
                    }
                } else {
                    $this->flashSession->error('wrong email or password');
                    return $this->response->redirect('index/login');
                }
            }
            return true;
        }
    }

    public function signupAction()
    {
        if ($this->session->has('AUTH_NAME') AND $this->session->has('AUTH_EMAIL') AND $this->session->has('AUTH_ROLE')) {
            return $this->response->redirect('index/index');
        } else {
            if ($this->request->isPost()) {
                $user = new Users();
                $security = new Security();

                $user->setName($this->request->getPost('name'));
                $user->setEmail($this->request->getPost('email'));
                $user->setRole('user');
                $user->setPassword($security->hash($this->request->getPost('password')));

                $success = $user->save();
                if ($success) {
                    $this->flashSession->success('Success');
                    return $this->response->redirect('index/index');
                } else {
                    $this->flashSession->error('Error message');
                    return $this->response->redirect('index/signup');
                }
            }
        }
    }

    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect('index/login');
    }

    public function error404Action(){

    }
}