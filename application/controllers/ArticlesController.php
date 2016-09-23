<?php

class ArticlesController extends AbstractController
{
    public function init(){
        parent::init();
        $this->view->breadcrumbs[] = array(
            'title' => "Статьи",
            'href' => "/articles",
        );
    }
    public function indexAction()
    {
        $newsTable = new Application_Model_Article_Table();
        $select = $newsTable->getAdapter()->select()
            ->from('Articles')
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('Date DESC');
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_ARTICLES)){
            $select->where('Visible = ?', 1);
        }
        $this->paginatorPrepare($select,10);
    }
    public function newAction(){
        $this->view->breadcrumbs[] = array(
            'title' => "Добавление статьи",
            'href' => "/articles/new",
        );
    }
    public function editAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_ARTICLES)){
            $this->_helper->redirector('index', 'articles');
        }
        $id = $this->getRequest()->getParam('id');
        if(is_null($id)){
            $this->_helper->redirector('index', 'articles');
        }
        $itemTable = new Application_Model_Article_Table();
        $this->view->item = $itemTable->fetchRow(array('Id = ?'=>$id));
        $this->view->breadcrumbs[] = array(
            'title' => "Редактирование статьи",
            'href' => "/articles/new",
        );
    }
    public function saveAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()){
            $this->_helper->redirector('index', 'articles');
        }
        $id = $this->getRequest()->getParam('id');
        $table = new Application_Model_Article_Table();
        if(!is_null($id)){
            $item = $table->fetchRow(array('Id = ?'=>$id));
        }else{
            $item = $table->createRow();
        }

        $item->setFromArray(array(
            'Date'=>date('Y-m-d H:i:s'),
            'Title'=>strip_tags($this->getRequest()->getParam('Title')),
            'Author'=>strip_tags($this->getRequest()->getParam('Author')),
            'Source'=>strip_tags($this->getRequest()->getParam('Source')),
            'Text'=>strip_tags($this->getRequest()->getParam('Text')),
            'UserId'=>$item->UserId?:Zend_Auth::getInstance()->getIdentity()->User->Id,
            'Visible'=>$this->getRequest()->getParam('Visible')?:0,
            'Alias'=>$item->Alias?:$this->getAlias(strip_tags($this->getRequest()->getParam('Title')), $table),
        ));

        if(isset($_FILES['Image'])){
            $iName = time().$_FILES['Image']['name'];
            if(move_uploaded_file($_FILES['Image']['tmp_name'], IMAGES_PATH.'/articles/'.$iName)){
                $item->Image = $iName;
            }
        }

        $item->save();

        $this->_helper->redirector('index', 'articles');
    }
    public function deleteAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_ARTICLES)){
            $this->_helper->redirector('index', 'articles');
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $itemTable = new Application_Model_Article_Table();
            $item = $itemTable->fetchRow(array('Id = ?'=>$id));
            if(!is_null($item)){
                $item->delete();
            }
        }
        $this->_helper->redirector('index', 'articles');
    }
    public function publishAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_ARTICLES)){
            $this->_helper->redirector('index', 'articles');
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $itemTable = new Application_Model_Article_Table();
            $item = $itemTable->fetchRow(array('Id = ?'=>$id));
            if(!is_null($item)){
                $item->Visible ^= 1;
                $item->save();
            }
        }
        $this->_helper->redirector('index', 'articles');
    }
    public function randomAction(){
        $this->_helper->layout()->disableLayout();
        $table = new Application_Model_Article_Table();
        $select = $table->getAdapter()->select()
            ->from('Articles')
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('RAND()')
            ->where('Visible = ?', 1)
            ->limit(1);

        $this->view->item = $table->getAdapter()->fetchRow($select);
    }
    public function singleAction(){
        $alias = $this->getRequest()->getParam('show');
        if(!$alias){
            $this->_helper->redirector('index', 'articles');
        }
        $table = new Application_Model_Article_Table();
        $select = $table->getAdapter()->select()
            ->from($table->info('name'))
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->where('Alias = ?', $alias);
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_ARTICLES)){
            $select->where('Visible = ?', 1);
        }
        $this->view->item = $table->getAdapter()->fetchRow($select);
        $this->view->breadcrumbs[] = array(
            'title' => $this->view->item->Title,
            'href' => "/articles/single/show/".$this->view->item->Alias,
        );
        if(!$this->view->item){
            $this->_helper->redirector('index', 'articles');
        }
    }
}

