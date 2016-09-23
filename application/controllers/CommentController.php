<?php

class CommentController extends AbstractController
{
    public function indexAction()
    {
        $id = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type');
        if(!is_null($id) && !is_null($type)){
            $comment = new Application_Model_Comment();
            $this->view->comments = $comment->getList($id, $type);
        }
    }
    public function newAction(){
        $this->_helper->layout()->disableLayout();
        $this->view->type = $this->getRequest()->getParam('type');
        $this->view->id = $this->getRequest()->getParam('id');
        $this->view->parent = $this->getRequest()->getParam('parent');
        $this->view->return = $this->getRequest()->getParam('ReturnUrl');
        if((!$this->view->type || !$this->view->id) && $this->view->parent){
            $table = new Application_Model_Comment_Table();
            $parent = $table->fetchRow(array('Id = ?' => $this->view->parent));
            if(!is_null($parent)){
                $this->view->type = $parent->ItemType;
                $this->view->id = $parent->ItemId;
            }
        }
    }
    public function editAction(){
//        if(!Zend_Auth::getInstance()->hasIdentity()
//            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
//            $this->_helper->redirector('index', 'books');
//        }
//        $id = $this->getRequest()->getParam('id');
//        if(is_null($id)){
//            $this->_helper->redirector('index', 'books');
//        }
//        $itemTable = new Application_Model_Book_Table();
//        $this->view->item = $itemTable->fetchRow(array('Id = ?'=>$id));
    }
    public function saveAction(){
        $type = $this->getRequest()->getParam('type');
        $id = $this->getRequest()->getParam('id');
        $parent = $this->getRequest()->getParam('parent');
        $redir = $this->getRequest()->getParam('ReturnUrl');

        if(!Zend_Auth::getInstance()->hasIdentity() || !isset(Application_Model_Comment::$typeControllers[$type])){
            $this->_redirect($redir);
        }
        $table = new Application_Model_Comment_Table();
        $item = $table->createRow();


        $item->setFromArray(array(
            'Date'=>date('Y-m-d H:i:s'),
            'Text'=>strip_tags($this->getRequest()->getParam('Text')),
            'UserId'=>Zend_Auth::getInstance()->getIdentity()->User->Id,
            'ParentId'=>$parent?:null,
            'ItemId'=>$id,
            'ItemType'=>$type,
        ));

        $item->save();

        $this->_redirect($redir);
    }
    public function deleteAction(){
//        if(!Zend_Auth::getInstance()->hasIdentity()
//            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
//            $this->_helper->redirector('index', 'books');
//        }
//        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);
//        $id = $this->getRequest()->getParam('id');
//        if(!is_null($id)){
//            $itemTable = new Application_Model_Book_Table();
//            $item = $itemTable->fetchRow(array('Id = ?'=>$id));
//            if(!is_null($item)){
//                $item->delete();
//            }
//        }
//        $this->_helper->redirector('index', 'books');
    }
}

