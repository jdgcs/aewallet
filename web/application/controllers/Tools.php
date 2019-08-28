<?php
if(!is_cli()){echo "NULL"; exit;}

class Tools extends CI_Controller {

	public function message($to = 'World')
	{
			echo "Hello {$to}!".PHP_EOL;
			$dir= realpath('.').'\db';
			echo $dir."\n";
			if($this->is_empty_dir($dir)){
				echo "empty\n";
				}else{echo "full\n";}
	}

	public function genAccount(){
		
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
