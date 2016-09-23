<?php

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $newsTable = new Application_Model_News_Table();
        $select = $newsTable->getAdapter()->select()
            ->from('News')
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('Date DESC');
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_NEWS)){
            $select->where('Visible = ?', 1);
        }
        $this->paginatorPrepare($select,20);
    }
    public function newAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_NEWS)){
            $this->_helper->redirector('index', 'index');
        }
        $this->view->breadcrumbs[] = array(
            'title' => "Добавление новости",
            'href' => "/index/new",
        );
    }
    public function editAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_NEWS)){
            $this->_helper->redirector('index', 'index');
        }
        $id = $this->getRequest()->getParam('id');
        if(is_null($id)){
            $this->_helper->redirector('index', 'index');
        }
        $itemTable = new Application_Model_News_Table();
        $this->view->item = $itemTable->fetchRow(array('Id = ?'=>$id));
        $this->view->breadcrumbs[] = array(
            'title' => "Редактирование новости",
            'href' => "/index/edit/id/".$this->view->item->Id,
        );
    }
    public function saveAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_NEWS)){
            $this->_helper->redirector('index', 'index');
        }
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $newsTable = new Application_Model_News_Table();
            $item = $newsTable->fetchRow(array('Id = ?'=>$id));
        }else{
            $newsTable = new Application_Model_News_Table();
            $item = $newsTable->createRow();
        }

        $item->setFromArray(array(
            'Date'=>date('Y-m-d H:i:s'),
            'Title'=>strip_tags($this->getRequest()->getParam('Title')),
            'Text'=>strip_tags($this->getRequest()->getParam('Text')),
            'UserId'=>Zend_Auth::getInstance()->getIdentity()->User->Id,
            'Visible'=>$this->getRequest()->getParam('Visible')?:0,
        ));
        if(isset($_FILES['Image'])){
            $iName = time().$_FILES['Image']['name'];
            if(move_uploaded_file($_FILES['Image']['tmp_name'], IMAGES_PATH.'/news/'.$iName)){
                $item->Image = $iName;
            }
        }

        $item->save();

        $this->_helper->redirector('index', 'index');
    }
    public function deleteAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_NEWS)){
            $this->_helper->redirector('index', 'index');
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $itemTable = new Application_Model_News_Table();
            $item = $itemTable->fetchRow(array('Id = ?'=>$id));
            if(!is_null($item)){
                $item->delete();
            }
        }
        $this->_helper->redirector('index', 'index');
    }
    public function publishAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_NEWS)){
            $this->_helper->redirector('index', 'index');
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $itemTable = new Application_Model_News_Table();
            $item = $itemTable->fetchRow(array('Id = ?'=>$id));
            if(!is_null($item)){
                $item->Visible ^= 1;
                $item->save();
            }
        }
        $this->_helper->redirector('index', 'index');
    }
}

