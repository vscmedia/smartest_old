<?php

class SmartestYahooDataDownloadHelper extends SmartestHelper{
    
    protected $_data = array();
    
    public function getDataAsNumericArray($symbol, $flags='sl1d1t1c1ohgv'){
        
        $url = "http://download.finance.yahoo.com/d/quotes.csv?s=".$symbol."&f=".$flags."&e=.csv";
        $filename = 'stockquote_'.SmartestStringHelper::toVarName($get['symbol']).'_'.time();
        
        $contents = SmartestHttpRequestHelper::getContent($url);
        SmartestFileSystemHelper::save(SM_ROOT_DIR.'System/Temporary/'.$filename.'.csv', $contents);
        
        $fh = fopen(SM_ROOT_DIR.'System/Temporary/'.$filename.'.csv', "r");
        
        while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
            $stock = $data;
        }
        
        fclose($fh);
        unlink(SM_ROOT_DIR.'System/Temporary/'.$filename.'.csv');
        
        return $stock;
        
    }
    
    public function getData($symbol, $flags='sl1d1t1c1ohgv'){
        
        $d = $this->getDataAsNumericArray($symbol, $flags);
        
        $data = new SmartestParameterHolder('Yahoo Stocks Information: '.$symbol);
        
        $flags_array = $this->parseFlags($flags);
        
        foreach($d as $k=>$v){
            $data->setParameter($flags_array[$k], $v);
        }
        
        return $data;
        
    }
    
    public function parseFlags($flags){
        
        preg_match_all('/[a-z]\d?/i', $flags, $matches);
        return $matches[0];
        
    }
    
}