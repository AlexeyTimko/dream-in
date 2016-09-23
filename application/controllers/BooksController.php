<?php

class BooksController extends AbstractController
{
    public function init(){
        parent::init();
        $this->view->breadcrumbs[] = array(
            'title' => "Книги",
            'href' => "/books",
        );
    }
    public function indexAction()
    {
        $newsTable = new Application_Model_Book_Table();
        $select = $newsTable->getAdapter()->select()
            ->from('Books')
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('Date DESC');
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
            $select->where('Visible = ?', 1);
        }
        $this->paginatorPrepare($select,10);
    }
    public function newAction(){
        $this->view->breadcrumbs[] = array(
            'title' => "Добавление книги",
            'href' => "/books",
        );
    }
    public function editAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
            $this->_helper->redirector('index', 'books');
        }
        $id = $this->getRequest()->getParam('id');
        if(is_null($id)){
            $this->_helper->redirector('index', 'books');
        }
        $itemTable = new Application_Model_Book_Table();
        $this->view->item = $itemTable->fetchRow(array('Id = ?'=>$id));
        $this->view->breadcrumbs[] = array(
            'title' => "Редактирование книги",
            'href' => "/books/edit/id/".$this->view->item->Id,
        );
    }
    public function saveAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()){
            $this->_helper->redirector('index', 'books');
        }
        $id = $this->getRequest()->getParam('id');
        $table = new Application_Model_Book_Table();
        if(!is_null($id)){
            $item = $table->fetchRow(array('Id = ?'=>$id));
        }else{
            $item = $table->createRow();
        }

        $item->setFromArray(array(
            'Date'=>date('Y-m-d H:i:s'),
            'Title'=>strip_tags($this->getRequest()->getParam('Title')),
            'Author'=>strip_tags($this->getRequest()->getParam('Author')),
            'Description'=>strip_tags($this->getRequest()->getParam('Description')),
            'UserId'=>$item->UserId?:Zend_Auth::getInstance()->getIdentity()->User->Id,
            'Visible'=>$this->getRequest()->getParam('Visible')?:0,
            'Alias'=>$item->Alias?:$this->getAlias(strip_tags($this->getRequest()->getParam('Title')), $table),
        ));

        if(isset($_FILES['Image'])){
            $iName = time().$_FILES['Image']['name'];
            if(move_uploaded_file($_FILES['Image']['tmp_name'], IMAGES_PATH.'/books/'.$iName)){
                $item->Image = $iName;
            }
        }
        if(isset($_FILES['File'])){
            $iName = time().$_FILES['File']['name'];
            if(move_uploaded_file($_FILES['File']['tmp_name'], PUBLIC_PATH.'/book_storage/'.$iName)){
                $item->File = $iName;
            }
        }

        $item->save();

        $this->_helper->redirector('index', 'books');
    }
    public function deleteAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
            $this->_helper->redirector('index', 'books');
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $itemTable = new Application_Model_Book_Table();
            $item = $itemTable->fetchRow(array('Id = ?'=>$id));
            if(!is_null($item)){
                $item->delete();
            }
        }
        $this->_helper->redirector('index', 'books');
    }
    public function publishAction(){
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
            $this->_helper->redirector('index', 'books');
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam('id');
        if(!is_null($id)){
            $itemTable = new Application_Model_Book_Table();
            $item = $itemTable->fetchRow(array('Id = ?'=>$id));
            if(!is_null($item)){
                $item->Visible ^= 1;
                $item->save();
            }
        }
        $this->_helper->redirector('index', 'books');
    }
    public function randomAction(){
        $this->_helper->layout()->disableLayout();
        $table = new Application_Model_Book_Table();
        $select = $table->getAdapter()->select()
            ->from($table->info('name'))
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('RAND()')
            ->where('Visible = ?', 1)
            ->limit(1);

        $this->view->item = $table->getAdapter()->fetchRow($select);
    }
    public function singleAction(){
        $alias = $this->getRequest()->getParam('show');
        if(!$alias){
            $this->_helper->redirector('index', 'books');
        }
        $table = new Application_Model_Book_Table();
        $select = $table->getAdapter()->select()
            ->from($table->info('name'))
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->where('Alias = ?', $alias);
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
            $select->where('Visible = ?', 1);
        }
        $this->view->item = $table->getAdapter()->fetchRow($select);
        if(!$this->view->item){
            $this->_helper->redirector('index', 'books');
        }
        $this->view->breadcrumbs[] = array(
            'title' => $this->view->item->Title,
            'href' => "/books/single/show/".$this->view->item->Alias,
        );
    }
}

