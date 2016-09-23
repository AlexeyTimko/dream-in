<?php
/**
 * Created by PhpStorm.
 * User: Timko
 * Date: 22.04.14
 * Time: 11:08
 */

class AuthController extends AbstractController{
    public function indexAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if(!$this->getRequest()->isPost()
            || is_null($email = $this->getRequest()->getParam('Email'))
            || is_null($pass = $this->getRequest()->getParam('Password'))
            || empty($email)
            || empty($pass)
        ){
            $this->_helper->redirector('index', 'index');
        }
        $authAdapter = new Zend_Auth_Adapter_DbTable ( Zend_Registry::get ( 'db' ), 'Users', 'Email', 'Password', '?' );

        $authAdapter->setIdentity ( $email )->setCredential ( md5($pass) );
        $auth = Zend_Auth::getInstance();
        $auth->authenticate($authAdapter);

        if(!$auth->hasIdentity()){
            $this->_helper->redirector('index', 'index');
        }

        $userTable = new Application_Model_User_Table();
        $user = $userTable->fetchRow(array('Email = ?' => $auth->getIdentity()));
        $params = new stdClass();
        $params->User = $user;
        $auth->getStorage()->write($params);
        $this->_helper->redirector('index', 'index');
    }
    public function registrationAction(){
        if($this->getRequest()->isPost()){
            $userTable = new Application_Model_User_Table();
            $user = $userTable->fetchRow(array('Email = ?' => $this->getRequest()->getParam('Email')));
            if(!is_null($user)){
                $this->view->error = 'Такой пользователь уже зарегистрирован';
            }else{
                $user = $userTable->createRow(array(
                    'Name' => $this->getRequest()->getParam('Name'),
                    'Email' => $this->getRequest()->getParam('Email'),
                    'Password' => md5($this->getRequest()->getParam('Password')),
                ));
                $user->save();
                $user->refresh();
                $authAdapter = new Zend_Auth_Adapter_DbTable ( Zend_Registry::get ( 'db' ), 'Users', 'Email', 'Password', '?' );

                $authAdapter->setIdentity ( $user->Email )->setCredential ( md5($user->Password) );
                $auth = Zend_Auth::getInstance();
                $auth->authenticate($authAdapter);

                $params = new stdClass();
                $params->User = $user;
                $auth->getStorage()->write($params);
                $this->_helper->redirector('index', 'index');
            }
        }
    }
    public function logoutAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index', 'index');
    }
} 