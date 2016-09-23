<?php

class DreamMapController extends AbstractController
{
    public function init(){
        parent::init();
        if(!Zend_Auth::getInstance()->hasIdentity()){
            $this->_helper->redirector('index', 'index');
        }
        $this->view->breadcrumbs[] = array(
            'title' => "Карта сновидений",
            'href' => "/dream-map",
        );
    }
    public function indexAction(){
        $fileName = IMAGES_PATH.'/maps/map_'.Zend_Auth::getInstance()->getIdentity()->User->Id.'.png';
        $this->view->map = file_exists($fileName)?$fileName:false;
    }
    public function saveAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $fileName = IMAGES_PATH.'/maps/map_'.Zend_Auth::getInstance()->getIdentity()->User->Id.'.png';
        $data = base64_decode(substr($_POST['data'], stripos($_POST['data'],',')+1));
        file_put_contents($fileName,$data);
    }
}

