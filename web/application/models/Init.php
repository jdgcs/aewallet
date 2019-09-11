<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Init extends CI_Model {
	
	public function newaccount(){
		$dir= realpath('.').'/db';
		if(!$this->is_empty_dir($dir)){
			echo "inited!";
			echo "<br><a href=/>Home</a>";
			exit;
			}
		$password=$this->input->post('password');		
		$password_repeat=$this->input->post('password_repeat');	
		if(($password!=$password_repeat) || $password==""){
			echo "Please set the (same) password.";
			exit;
		}
			
		///account init///
		$cmd=realpath('.').'/../env/signtx.exe account create home --password '.$password;		
		exec($cmd,$ret);
		$tmpstr=explode(" ",$ret[1]);
		$account= $tmpstr[count($tmpstr)-1];
		echo "Account=>$account<br>";
		//$dir=".\\db\\$account";
		//mkdir ($dir,0777,true);
		$dir_file="./db/$account";
		copy("home",$dir_file);
		unlink("home");
		$fp = fopen($dir_file, "r");
		$file_read = fread($fp, filesize($dir_file));	
		
			
		echo "<br><a href=/index.php>Home</a>";
		}
	
	public function base58_encode($string)
{
    $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
    $base = strlen($alphabet);

    if (is_string($string) === false || !strlen($string)) {
        return false;
    }

    $bytes = array_values(unpack('C*', $string));
    $decimal = $bytes[0];
    for ($i = 1, $l = count($bytes); $i < $l; ++$i) {
        $decimal = bcmul($decimal, 256);
        $decimal = bcadd($decimal, $bytes[$i]);
    }

    $output = '';
    while ($decimal >= $base) {
        $div = bcdiv($decimal, $base, 0);
        $mod = bcmod($decimal, $base);
        $output .= $alphabet[$mod];
        $decimal = $div;
    }
    if ($decimal > 0) {
        $output .= $alphabet[$decimal];
    }
    $output = strrev($output);

    return (string) $output;
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
