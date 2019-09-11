<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends CI_Model {	
	
public function postTx($tx,$pubnode,$ak){
	$jsonStr ='{ "tx": "'.$tx.'"}';
	$info=$this->http_post_json($pubnode, $jsonStr);
	$return= json_decode(stripslashes($info)); 
	
	$data['info']= $info;
	if(strpos($info,"tx_hash")>0){
		$tx_hash=$return->tx_hash;
		$url="<a href=https://www.aeknow.org/block/transaction/$tx_hash target=_blank>$tx_hash</a>";
		$data['info']= $url;
	}
	
	$data['ak']=$ak;
	$data['username']=$ak;
	
	return $data;
	}

public function sendTx($sender_id,$recipient_id,$amount,$password,$payload="",$gas=0,$nonce=0,$pubnode=""){
	$data['amount']=$amount;
	
	if($gas==0){$gas=0.00005;}
	
	$data['gas']=number_format($gas,5,".","");
	
	if(!$this->checkAddress($recipient_id)){
		if(strpos($recipient_id,".test")>0 || strpos($recipient_id,".aet")>0 ){
			$recipient_id=$this->getAENS($recipient_id);
			if(!$this->checkAddress($recipient_id)){
				echo "BAD recipient_id";
				exit ;
				}
			}else{
				echo "BAD recipient_id";
				exit ;
				}
	}
	
	$amount=number_format($amount*1000000000000000000,0,"","");	
	$gas=number_format($gas*1000000000000000000,0,"","");
	
	if($pubnode==""){
		$url = PUBLIC_NODE."/v2/transactions";
	}
	
	if($nonce==0){
		$nonce=$this->getNonce($sender_id);
	}
	$data['nonce']=$nonce;
	$ttl=0;
	$tx="";
	
	$cmd=realpath('.')."/../env/signtx.exe tx spend $sender_id $recipient_id $amount \"$payload\" --nonce $nonce --ttl $ttl --fee $gas";	
	exec($cmd,$ret);
	$tmpstr=explode(" ",$ret[7]);
	$tx= $tmpstr[count($tmpstr)-1];
	$cmd=realpath('.')."/../env/signtx.exe account sign ".realpath('.')."/db/$sender_id $tx --password $password -n ae_mainnet";
	exec($cmd,$ret);

	if(strpos($ret[8],"account address")>0){
		$tmpstr=explode(" ",$ret[11]);
		$tx_signed= $tmpstr[count($tmpstr)-1];		
		$data['tx']=$tx_signed;
		
		$tmpstr=explode(" ",$ret[12]);
		$txhash= $tmpstr[count($tmpstr)-1];		
		$data['txhash']=$txhash;
	}else{
		$data['tx']="Error:".$ret[8];
		}
	
	$data['pubnode']=$url;
	$data['ak']=$sender_id;
	$data['sender_id']=$sender_id;
	$data['recipient_id']=$recipient_id;	
	$data['payload']=$payload;
	
	
	$info=$this->getInfo($sender_id);
	$data['username']=$info['username'];
	
	$tx=$data['tx'];
	$jsonStr ='{ "tx": "'.$tx.'"}';

	$info=$this->http_post_json($pubnode, $jsonStr);
	$return= json_decode(stripslashes($info)); 
	if(strpos($info,"tx_hash")>0){
		$tx_hash=$return->tx_hash;
		$url="<a href=https://www.aeknow.org/block/transaction/$tx_hash >$tx_hash</a>";
		echo $url;
	}else{
		echo $info;
		}

	}
	
			
public function generateTx($sender_id,$recipient_id,$amount,$password,$payload="",$gas=0,$nonce=0,$pubnode=""){
	$data['amount']=$amount;
	
	if($gas==0){$gas=0.00005;}
	
	$data['gas']=number_format($gas,5,".","");
	
	if(!$this->checkAddress($recipient_id)){
		if(strpos($recipient_id,".test")>0 || strpos($recipient_id,".aet")>0 ){
			$recipient_id=$this->getAENS($recipient_id);
			if(!$this->checkAddress($recipient_id)){
				echo "BAD recipient_id";
				exit ;
				}
			}else{
				echo "BAD recipient_id";
				exit ;
				}
	}
	
	$amount=number_format($amount*1000000000000000000,0,"","");	
	$gas=number_format($gas*1000000000000000000,0,"","");
	
	if($pubnode==""){
		$url = PUBLIC_NODE."/v2/transactions";
	}else{
		$url = $pubnode."/v2/transactions";
		}
	
	if($nonce==0){
		$nonce=$this->getNonce($sender_id);
	}
	$data['nonce']=$nonce;
	$ttl=0;
	$tx="";
	
	$cmd=realpath('.')."/../env/signtx.exe tx spend $sender_id $recipient_id $amount \"$payload\" --nonce $nonce --ttl $ttl --fee $gas";	
	//echo $cmd;
	exec($cmd,$ret);
	$tmpstr=explode(" ",$ret[7]);
	$tx= $tmpstr[count($tmpstr)-1];
	$cmd=realpath('.')."/../env/signtx.exe account sign ".realpath('.')."/db/$sender_id $tx --password $password -n ae_mainnet";
	exec($cmd,$ret);

	if(strpos($ret[8],"account address")>0){
		$tmpstr=explode(" ",$ret[11]);
		$tx_signed= $tmpstr[count($tmpstr)-1];		
		$data['tx']=$tx_signed;
		
		$tmpstr=explode(" ",$ret[12]);
		$txhash= $tmpstr[count($tmpstr)-1];		
		$data['txhash']=$txhash;
	}else{
		$data['tx']="Error:".$ret[8];
		}
	
	$data['pubnode']=$url;
	$data['ak']=$sender_id;
	$data['sender_id']=$sender_id;
	$data['recipient_id']=$recipient_id;	
	$data['payload']=$payload;
	
	
	$info=$this->getInfo($sender_id);
	$data['username']=$info['username'];
	return $data;
	}


public function checkDate($recipient_id,$amount,$password){
	$data['info']="OK";	
	if (trim($recipient_id)==""){$data['info']="recipient_id is null.<br>";} 
	if (trim($amount)==""){$data['info']="amount is null.<br>";} 
	if (!is_numeric($amount)){$data['info']="amount must be number.<br>";} 
	if (trim($password)==""){$data['info']="password is null.<br>";} 	
	return $data;
	}
	
public function getInfo($ak){	
	$data['ak']=$ak;
	$data['username']=$data['ak'];
	
	return $data;
	}


public function getAENS($aens){
	$ak="";
	$url=PUBLIC_NODE."/v2/names/$aens";	
	$websrc=$this->getwebsrc($url);
	$tagstr='pointers';
	if(strpos($websrc,$tagstr)>0){
		$info=json_decode($websrc);
		$ak=$info->pointers[0]->id;
		}
	
	return $ak;
	}

public function checkAddress($address){
		$address=str_replace("ak_","",$address);
		$address=str_replace("ok_","",$address);
        $hex = $this->base58_decode($address);
    
        if (strlen($hex)!=72){
            return false;
        }
    
        $bs = pack("H*", substr($hex, 0,64));    
        $checksum = hash("sha256", hash("sha256", $bs, true));    
        $checksum = substr($checksum, 0, 8);    
        if(substr($hex, 64,8)!==$checksum){
            return false;
        }
    
        return true;
    
    }

public function base58_decode($base58)
    {
        $origbase58 = $base58;
        $return = "0";
    
        for ($i = 0; $i < strlen($base58); $i++) {
            // return = return*58 + current position of $base58[i]in self::$base58chars
            $return = gmp_add(gmp_mul($return, 58), strpos("123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz", $base58[$i]));
        }
        $return = gmp_strval($return, 16);
        for ($i = 0; $i < strlen($origbase58) && $origbase58[$i] == "1"; $i++) {
            $return = "00" . $return;
        }
        if (strlen($return) % 2 != 0) {
            $return = "0" . $return;
        }
        return $return;
    }



function getNonce($ak){
	$url=PUBLIC_NODE."/v2/accounts/$ak";	
	
	$websrc=$this->getwebsrc($url);
	//echo "$url\n$websrc\n";
	$tagstr='"nonce":';
	if(strpos($websrc,$tagstr)>0){
		$tmpstr=explode($tagstr,$websrc);
		$currentnonce=str_replace("}","",$tmpstr[1]);
		$newnonce=$currentnonce+1;
		return $newnonce;
	}
	
	return 0;
}

public function http_post_json($url, $jsonStr)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr)
        )
    );
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
	//echo "<br/>$url==>>resp: $response";
    return $response;// array($httpCode, $response);
}


public function object_array($array)
		{
		   if(is_object($array))
		   {
			$array = (array)$array;
		   }
		   if(is_array($array))
		   {
			foreach($array as $key=>$value)
			{
			 $array[$key] = $this->object_array($value);
			}
		   }
		   return $array;
		}	
	

function GetTopHeight()	{
	$url=DATA_SRC_SITE."v2/blocks/top";
	$websrc=$this->getwebsrc($url);
	$info=json_decode($websrc);
	if(strpos($websrc,"key_block")==TRUE){		
		return $info->key_block->height;
	}
		
	if(strpos($websrc,"micro_block")==TRUE){
		return $info->micro_block->height;
		}
	
	return 0;
	}


private function getwebsrc($url) {
	$curl = curl_init ();
	$agent = "User-Agent: AE-bot 1.0.0";
	
	curl_setopt ( $curl, CURLOPT_URL, $url );

	//curl_setopt ( $curl, CURLOPT_USERAGENT, $agent );
	curl_setopt ( $curl, CURLOPT_ENCODING, 'gzip,deflate' );
	curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 ); //×¥È¡301Ìø×ªºóÍøÖ·
	curl_setopt ( $curl, CURLOPT_AUTOREFERER, true );
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $curl, CURLOPT_TIMEOUT, 60 );
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	
	$html = curl_exec ( $curl ); // execute the curl command
	$response_code = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
	if ($response_code != '200') { //Èç¹ûÎ´ÄÜ»ñÈ¡¸ÃÒ³Ãæ£¨·Ç200·µ»Ø£©£¬ÔòÖØÐÂ³¢ÊÔ»ñÈ¡
	//	echo 'Page error: ' . $response_code . $html;	
		$html='Page error: ' . $response_code.$html;
	} 
	curl_close ( $curl ); // close the connection

	return $html; // and finally, return $html
}

}
