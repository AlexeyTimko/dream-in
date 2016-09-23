<?php
/**
 * Created by PhpStorm.
 * User: Timko
 * Date: 22.04.14
 * Time: 11:08
 */

class SitemapController extends AbstractController{
    public function indexAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header("Content-type: text/xml; charset=utf-8");
        print '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9';
        print ' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        print '<url><loc>http://dream-in.net/</loc><changefreq>always</changefreq><priority>1.00</priority></url>';
        print '<url><loc>http://dream-in.net/articles/</loc><changefreq>always</changefreq><priority>1.00</priority></url>';
        print '<url><loc>http://dream-in.net/books/</loc><changefreq>always</changefreq><priority>1.00</priority></url>';
        $table = new Application_Model_Article_Table();
        foreach($table->fetchAll('Visible = 1') as $item){
            printf('<url><loc>http://dream-in.net/articles/single/show/%s/</loc><changefreq>always</changefreq><priority>0.80</priority></url>', $item->Alias);
        }
        $table = new Application_Model_Book_Table();
        foreach($table->fetchAll('Visible = 1') as $item){
            printf('<url><loc>http://dream-in.net/books/single/show/%s/</loc><changefreq>always</changefreq><priority>0.80</priority></url>', $item->Alias);
        }
        print '</urlset>';
    }
}