<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends CI_Controller {

	public function index()
	{
		
	}
	
	public function show($ak){		
		$this->load->model('wallets');
		$data=$this->wallets->getAccount($ak);	
		
		$this->load->model('languages');	
		$data['mylang']=$this->languages->getPreferredLanguage();	
		$this->load->view('wallet',$data);
	}
	
	public function other($ak){
		$data['ak']=$ak;
		$data['username']=$ak;
		$this->load->model('languages');	
		$data['mylang']=$this->languages->getPreferredLanguage();	
		$this->load->view('wallets',$data);
		}
	
	public function tx($ak){		
		$this->load->model('wallets');
		$data=$this->wallets->getTxHistory($ak);		
		$this->load->view('wallet_tx',$data);
	}
	
	
	public function gentx(){
		$this->input->post();
		$sender_id= $this->input->post('sender_id');
		$recipient_id= $this->input->post('recipient_id');
		$amount= $this->input->post('amount');
		$password= $this->input->post('password');
		$payload= $this->input->post('payload');
		$gas= $this->input->post('gas');
		$nonce= $this->input->post('nonce');
		$pubnode= $this->input->post('pubnode');
		
			
		$this->load->model('transactions');
		
		$check=$this->transactions->checkDate($recipient_id,$amount,$password);
		if($check['info']=="OK"){
			$data=$this->transactions->generateTx($sender_id,$recipient_id,$amount,$password,$payload,$gas,$nonce,$pubnode);		
			$this->load->model('languages');	
			$data['mylang']=$this->languages->getPreferredLanguage();	
		
			$this->load->view('transaction',$data);
		}else{
			echo "ERROR:". $check['info'];
			}
	}
	
	public function posttx(){
		$this->input->post();
		$tx= $this->input->post('tx');
		$pubnode= $this->input->post('pubnode');
		$ak= $this->input->post('sender_id');
		$this->load->model('transactions');
		$data=$this->transactions->postTx($tx,$pubnode,$ak);	
		
			$this->load->model('languages');	
			$data['mylang']=$this->languages->getPreferredLanguage();	
			
			$data['ak']=$ak;
		
			$this->load->view('posttx',$data);	
		}
		
	public function getimg($str=NULL){
		$this->load->library('Qrcode'); 
		$str=urldecode($str);
		echo $this->qrcode->png($str);		
		}
		
		
}
