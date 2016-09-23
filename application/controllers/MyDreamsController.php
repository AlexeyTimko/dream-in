<?php

class MyDreamsController extends AbstractController
{
    public function init(){
        parent::init();
        if(!Zend_Auth::getInstance()->hasIdentity()){
            $this->_helper->redirector('index', 'index');
        }
        $this->view->breadcrumbs[] = array(
            'title' => "Мои сны",
            'href' => "/my-dreams",
        );
    }
    public function indexAction()
    {
        $dreamTable = new Application_Model_Dream_Table();
        $select = $dreamTable->select()
            ->where('UserId = ?', Zend_Auth::getInstance()->getIdentity()->User->Id)
            ->order('Date DESC');
        $this->paginatorPrepare($select);
    }
    public function newAction(){
        $this->view->breadcrumbs[] = array(
            'title' => "Новый сон",
            'href' => "/my-dreams/new",
        );
    }
    public function editAction(){
        $id = $this->getRequest()->getParam('id');
        if(is_null($id)){
            $this->_helper->redirector('index', 'my-dreams');
        }
        $dreamTable = new Application_Model_Dream_Table();
        $this->view->dream = $dreamTable->fetchRow(array('Id = ?'=>$id));
        $this->view->breadcrumbs[] = array(
            'title' => "Изменение сна",
            'href' => "/my-dreams/edit/id/".$this->view->dream->Id,
        );
    }
    public function saveAction(){
        $id = $this->getRequest()->getParam('id');
        $table = new Application_Model_Dream_Table();
        if(!is_null($id)){
            $dream = $table->fetchRow(array('Id = ?'=>$id));
        }else{
            $dream = $table->createRow();
        }

        $dream->setFromArray(array(
            'Date'=>date('Y-m-d H:i:s'),
            'Title'=>strip_tags($this->getRequest()->getParam('Title')),
            'Text'=>strip_tags($this->getRequest()->getParam('Text')),
            'UserId'=>Zend_Auth::getInstance()->getIdentity()->User->Id,
            'Alias'=>$dream->Alias?:$this->getAlias(strip_tags($this->getRequest()->getParam('Title')), $table),
        ));
        $dream->save();

        $this->_helper->redirector('index', 'my-dreams');
    }
    public function deleteAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $dreamTable = new Application_Model_Dream_Table();
            $dream = $dreamTable->fetchRow(array('Id = ?'=>$id));
            if(!is_null($dream)){
                $dream->delete();
            }
        }
        $this->_helper->redirector('index', 'my-dreams');
    }
}

