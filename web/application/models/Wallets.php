<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wallets extends CI_Model {

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

public function getAccount($ak){
	$url=PUBLIC_NODE."/v2/accounts/$ak";	
	
	$data['balance']=0;
	$data['nonce']=0;
	$data['ak']=$ak;
	
	$data['username']="None";	
	$data['isonline']="<small class=\"label bg-yellow\">Offline</small>";
	
	
		
	$websrc=$this->getwebsrc($url);
	$tagstr='"nonce":';
	
	if(strpos($websrc,$tagstr)>0){
		$data=$this->object_array(json_decode($websrc));
		//$info=$this->getInfo($ak);
		//$data['username']=$info['username'];
		
		$data['ak']=$data['id'];
		$data['username']=$data['ak'];
		
		$data['isonline']="<small class=\"label bg-green\">Online</small>";
		//return $data;
	}
	
	return $data;
}


public function getTxHistory($ak){
	$url=PUBLIC_NODE."/v2/tx/$ak";
	$data['ak']=$ak;	
	$data['username']="None";
	
	$websrc=$this->getwebsrc($url);
	$tagstr='txhash';
	$data['totaltxs']="";
	if(strpos($websrc,$tagstr)>0){
		$info=json_decode($websrc);
		$count=count($info->txs);
		for($i=0;$i<$count;$i++){
			$txhash=$info->txs[$i]->txhash;  			
			$txtype=$info->txs[$i]->txtype;	
			$block_height=$info->txs[$i]->block_height;
			$time=$block_height;
			
			
			if($txtype=='SpendTx'){				
				$txhash_show="th_****".substr($txhash,-4);
				$amount=round($info->txs[$i]->amount/1000000000000000000,6);
				$recipient_id=$info->txs[$i]->recipient_id;			
				$recipient_id_show="ak_****".substr($recipient_id,-4);
				
				$sender_id=$info->txs[$i]->sender_id;
				$sender_id_show="ak_****".substr($sender_id,-4);
				
				
				if($sender_id==$ak){
						$senderlink="$sender_id_show";
						$recipientlink="<span class='badge bg-yellow'>OUT</span><a href='https://www.aeknow.org/address/wallet/$recipient_id'>$recipient_id_show</a>";
					}else{
						$senderlink="<a href='https://www.aeknow.org/address/wallet/$sender_id'>$sender_id_show</a>";
						$recipientlink="<span class='badge bg-green'>&nbsp; IN &nbsp; </span>$recipient_id_show";
					}
			
				
				$data['totaltxs'].="<tr><td><a href=https://www.aeknow.org/block/transaction/$txhash>$txhash_show</a></td><td>$amount</td><td>$senderlink</td><td>$recipientlink</td><td>$txtype</td><td>$time</td></tr>";
			}else{
				$data['totaltxs'].="<tr><td colspan=\"4\"><a href=https://www.aeknow.org/block/transaction/$txhash>$txhash</a></td><td>$txtype</td><td>$time</td></tr>";		
				}
			}
			//$data['txs'].="<li><a href=https://www.aeknow.org/block/transaction/$txhash>$txtype</a> $amount</li>\n";
			//$data['txs'].="<tr><td>$txhash</td><td>$amount</td><td>$sender_id</td><td>$recipient_id</td><td>$txtype</td><td>$block_height</td></tr>";
			}
		
		
	$info=$this->getInfo($ak);
	$data['username']=$info['username'];
	$data['ak']=$ak;
	
	return $data;
	}
	
	



public function getNonce($ak){
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
    return array($httpCode, $response);
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
