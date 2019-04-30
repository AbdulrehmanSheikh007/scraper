<?php

require("simple_html_dom.php");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Scrapper
 *
 * @author Abdul Rehman
 */
class Scrapper {

    protected $search;
    protected $amazone_url;
    protected $daraz_url;
    protected $amazon_data = array();
    protected $daraz_data = array();
    protected $json_file = "products-details.txt";
    protected $total_pages = 5;

    public function __construct($searchQry = "") {
        $this->search = $searchQry;
        $this->amazone_url = "https://www.amazon.com/s?k=";
        $this->daraz_url = "https://www.daraz.pk/catalog/?q=";
    }

    //Scrap Amazon Products 
    public function scrapAmazon() {
        $html = file_get_html($this->amazone_url . $this->search);
//        $totalPages = (int) $html->find(".a-pagination>.a-disabled")[1]->plaintext;
        for ($i = 1; $i <= $this->total_pages; $i++) {
            $html = file_get_html($this->amazone_url . $this->search . "&page=" . $i . "&s=price-asc-rank");
            $this->parseAmazonData($html);
        }

        $this->appendJSON();
    }

    //Scrapped Data Parser 
    public function parseAmazonData($html) {
        $dataCollection = array();
        foreach ($html->find(".sg-col-20-of-24") as $item) {
            $data['name'] = (isset($item->find('span.a-size-medium.a-color-base.a-text-normal')[0]) && !empty($item->find('span.a-size-medium.a-color-base.a-text-normal')[0])) ? $item->find('span.a-size-medium.a-color-base.a-text-normal')[0]->plaintext : "";
            $data['image_url'] = (isset($item->find(".s-image")[0]) && !empty($item->find(".s-image")[0])) ? $item->find(".s-image")[0]->src : "";
            $data['price'] = (isset($item->find('span.a-offscreen')[0]) && !empty($item->find('span.a-offscreen')[0])) ? $item->find('span.a-offscreen')[0]->plaintext : "";
            $data['platform'] = "Amazon";

            if (empty($data['price']) || empty($data['name'])) {
                continue;
            }

            $this->amazon_data[] = $data;
        }
    }
    
    //Scrap Daraz Products
    public function scrapDaraz(){
        for($i=1; $i<=$this->total_pages; $i++){
            $html = file_get_html($this->daraz_url . $this->search . "&page=" . $i . "&sort=priceasc");
            $this->parseDarazData($html);
        }

        $this->appendJSON();
    }
    
    //Scrapped Data Parser
    public function parseDarazData($html){
        $html = $html->find('script[type="application/ld+json"]')[1]; 
        $html = str_replace('<script type="application/ld+json">', '', $html); 
        $html = str_replace('</script>', '', $html); 
        $html = str_replace('@', '', $html); 
        foreach(json_decode($html)->itemListElement as $item)
        {
            $data['name'] = $item->name; 
            $data['image_url'] = $item->image;
            $data['price'] = $item->offers->priceCurrency . $item->offers->price;
            $data['platform'] = "Daraz";
            $this->daraz_data[] = $data;
        }
    }

    //Append JSON in File
    public function appendJSON() {
        if (!empty($this->amazon_data)) {
            $json = json_encode($this->amazon_data);
            file_put_contents($this->json_file, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        if (!empty($this->daraz_data)) {
            $json = json_encode($this->daraz_data);
            file_put_contents($this->json_file, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        return true;
    }

    //Get JSON Data to Display
    public function getJSON(){
        return @file_get_contents($this->json_file); 
    }
    
    //generateHTML
    public function renderHTML(){
        $json = $this->getJSON();
        $html = ""; 
        if(!empty($json))
        {
            $json = json_decode($json);
            foreach($json as $item)
            {
                $html .= include("template.php");
            }
        }
        
        return $html; 
    }
}

if(isset($_REQUEST['searchQry']) && !empty($_REQUEST['searchQry']))
{
    $obj = new Scrapper($_REQUEST['searchQry']);
    $obj->scrapAmazon();
    $obj->scrapDaraz();
    echo $obj->renderHTML(); 
}
