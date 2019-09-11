<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$dir= realpath('.').'/db';	
		
		if($this->is_empty_dir($dir)){
			$this->load->library('form_validation');
			
			$this->load->model('languages');	
			$data['mylang']=$this->languages->getPreferredLanguage();
			
			$this->load->view('register',$data);
		}else{			
			$data=$this->showHome();
			$ak=$data['ak'];
			$this->load->model('wallets');
			$data=$this->wallets->getAccount($ak);	
			
			$this->load->model('languages');	
			$data['mylang']=$this->languages->getPreferredLanguage();
				
			$this->load->view('wallet',$data);
			//$this->load->view('home',$data);
		}
	}
	
	
	public function register(){
		$this->load->model('init');
		$data=$this->init->newaccount();		
		//$this->load->view('wallet',$data);
		}
	
	public function showHome(){
		$dir= realpath('.').'/db';
		$data['ak']=$this->GetAKName($dir);		
		$data['username']=$data['ak'];
		
		return $data;
	}
	
	

public function GetAKName($dir) {
	global $bloger;
	//global $newbloger;
	$counter=0;
	//echo $dir;
	$dir = realpath ( $dir );
	$stack = array ($dir );
	while ( NULL !== ($dir = array_shift ( $stack )) && false !== ($handle = opendir ( $dir )) ) {
		while ( $file = readdir ( $handle ) ) {
			if ($file == '.' || $file == "..") {
				continue 1;
			}
			$path = $dir . '/' . $file;
			if (is_dir ( $path )) {
				array_push ( $stack, $path );
			} else {
				return basename($path);
			}
		}
	}
return "";
}

	
    
    
public function getPreferredLanguage() {  
    $langs = array();  
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {  
        // break up string into pieces (languages and q factors)  
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)s*(;s*qs*=s*(1|0.[0-9]+))?/i',  
                $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);  
        if (count($lang_parse[1])) {  
            // create a list like "en" => 0.8  
            $langs = array_combine($lang_parse[1], $lang_parse[4]);  
            // set default to 1 for any without q factor  
            foreach ($langs as $lang => $val) {  
                if ($val === '') $langs[$lang] = 1;  
            }  
            // sort list based on value  
            arsort($langs, SORT_NUMERIC);  
        }  
    }  
    //extract most important (first)  
    foreach ($langs as $lang => $val) { break; }  
    //if complex language simplify it  
    if (stristr($lang,"-")) {$tmp = explode("-",$lang); $lang = $tmp[0]; }  
    return $lang;  
}  

public  function is_empty_dir($fp)    
    {    
        $H = @opendir($fp); 
        $i=0;    
        while($_file=readdir($H)){    
            $i++;    
        }    
        closedir($H);    
        if($i>2){ 
            return false; 
        }else{ 
            return true;  //true
        } 
    }
}
