<?php

class Help extends CI_Controller {

	public function index()
	{
		echo "Help Page";
	}
	
	
	public function view($ak,$lang){
		$helpfile="help/help_$lang";
		//echo "file:".$lang;
		$this->load->model('languages');	
		$data['mylang']=$this->languages->getPreferredLanguage();	
		$data['ak']=$ak;
		$data['username']=$ak;
		
		$this->load->view($helpfile,$data);
		}
	}
