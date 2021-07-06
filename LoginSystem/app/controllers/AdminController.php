<?php

use Phalcon\Security;
use Phalcon\Escaper;
use Phalcon\Flash\Direct;

class AdminController extends ControllerBase
{

    public function indexAction()
    {
        if ($this->session->has('AUTH_NAME') AND $this->session->has('AUTH_EMAIL') AND $this->session->has('AUTH_ROLE') AND $this->session->get("AUTH_ROLE") === 'admin') {
            $this->view->users = Users::find();
            return true;
        }
        $this->flashSession->warning('You have no permission');
        return $this->response->redirect('index/index');
    }

    public function createAction()
    {
        $this->view->users = Users::find();
        if ($this->session->has('AUTH_NAME') AND $this->session->has('AUTH_EMAIL') AND $this->session->has('AUTH_ROLE') AND $this->session->get("AUTH_ROLE") === 'admin') {
            if ($this->request->isPost()) {
                $user = new Users();
                $security = new Security();

                $user->setName($this->request->getPost('name'));
                $user->setEmail($this->request->getPost('email'));
                $user->setRole($this->request->getPost('role'));
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
            return true;
        }
        $this->flashSession->warning('You have no permission');
        return $this->response->redirect('index/index');
    }

    public function editAction($userId)
    {
        if ($this->session->has('AUTH_NAME') AND $this->session->has('AUTH_EMAIL') AND $this->session->has('AUTH_ROLE') AND $this->session->get("AUTH_ROLE") === 'admin') {
            $user = Users::findFirstById($userId);
            $this->view->user = $user;

            if ($this->request->isGet()) {
                if (!empty($userId)) {
                    $user = Users::findFirstById($userId);

                    $user->setName($this->request->get('name'));
                    $user->setEmail($this->request->get('email'));
                    $user->setRole($this->request->get('role'));
                    $user->setPassword($this->request->get('password'));

                    if ($this->request->get('submit')) {
                        $success = $user->save();
                        if ($success) {
                            $this->flashSession->success('Success');
                            return $this->response->redirect('admin/index');
                        } else {
                            $this->flashSession->error("ERROR: Couldn't save");
                            return $this->response->redirect('admin/index');
                        }
                    }
                }
            }
            return true;
        }
        $this->flashSession->warning('You have no permission');
        return $this->response->redirect('index/index');
    }

    public function deleteAction($userId)
    {
        if ($this->session->has('AUTH_NAME') AND $this->session->has('AUTH_EMAIL') AND $this->session->has('AUTH_ROLE') AND $this->session->get("AUTH_ROLE") === 'admin') {
            $user = Users::findFirstById($userId);
            if (!$user->delete()) {
                $this->flashSession->error("Cannot delete user!");
                return $this->response->redirect('admin/index');
            } else {
                $this->flashSession->success('User deleted');
                return $this->response->redirect('admin/index');
            }
            return true;
        }
        $this->flashSession->warning('You have no permission');
        return $this->response->redirect('index/index');
    }

}