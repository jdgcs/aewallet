<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oracles extends CI_Model {

public function finishOracle(){	
	$this->input->post();
	$oracle_id= $this->input->post('oracle_id');
	$oracle_query= $this->input->post('oracle_query');	
	$checkoption=$this->input->post('checkoption');
	$oid= $this->input->post('oid');
	$data['oid']=$oid;
	//echo $checkoption;
	
	$data['ak']="";
	$data['username']="";
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	
	$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
	$results = $db->query($sql);

	while ($row = $results->fetchArray()) {
		$data['ak']=$row['ak'];
		$data['peerid']=$row['peerid'];
		$data['username']=$row['username'];
		$data['recommend']=$row['recommend'];
	}
	
	$data['finishstats']=$this->getFinishStats($oid,$checkoption);
	return $data;
	}
	
public function postOracle(){
	$this->input->post();
	$oracle_id= $this->input->post('oracle_id');
	$oracle_query= $this->input->post('oracle_query');
	$password= $this->input->post('password');
	$oid= $this->input->post('oid');
	
	$data['ak']="";
	$data['username']="";
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	
	$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
	$results = $db->query($sql);

	while ($row = $results->fetchArray()) {
		$data['ak']=$row['ak'];
		$data['peerid']=$row['peerid'];
		$data['username']=$row['username'];
		$data['recommend']=$row['recommend'];
	}
	
	
	//$url = "http://localhost:3113/v2/debug/oracles/query";
	$url =PUBLIC_NODE . "/v2/debug/oracles/query";
	$query=$oracle_query;
	$ak=$data['ak'];
	$sender_id=$ak;
	
	$nonce=$this->getNonce($ak);
	$ttl=$this->GetTopHeight()+6;
	$jsonStr="{ \"oracle_id\": \"$oracle_id\", \"response_ttl\": { \"type\": \"delta\", \"value\": 3 }, \"query\": \"$query\", \"query_ttl\": { \"type\": \"delta\", \"value\": 6 }, \"fee\": 20000000000000, \"query_fee\": 1000000000000000000, \"ttl\": $ttl, \"nonce\": $nonce, \"sender_id\": \"$ak\"}";
	//echo "$jsonStr";
	$return= $this->http_post_json($url, $jsonStr); 
	//print_r($return);
	$keys=json_decode($return[1]); 
	$tx_unsigned=$keys->tx;
	$cmd=realpath('.')."\\..\\env\\signtx.exe account sign ".realpath('.')."\\db\\$sender_id\\home $tx_unsigned --password $password -n ae_mainnet";
	
	exec($cmd,$ret);
	//echo "$cmd<br/>";
	//print_r($ret);

	if(strpos($ret[3],"igned")>0){
		$tmpstr=explode(" ",$ret[3]);
		$tx_signed= $tmpstr[count($tmpstr)-1];		
		$data['tx']=$tx_signed;
		
		$tmpstr=explode(" ",$ret[4]);
		$txhash= $tmpstr[count($tmpstr)-1];		
		$data['txhash']=$txhash;
	}else{
		$data['tx']="Error:".$ret[4];
		}
	
	$pubnode = PUBLIC_NODE."/v2/transactions";
	$info=$this->postTx($tx_signed,$pubnode);
	$info_tx=json_decode($info[1]);
	$data['txhash']="";
	$data['txhash']=$info_tx->tx_hash;
	$data['response']="NULL";
	
	if(strlen($data['txhash'])>0){
		//sleep(5);
		//$result=$this->getQueryResult($ak,$oracle_id,$oracle_query);
		//$result=str_replace("or_","",$result);
		//$data['response']=base64_decode(base64_decode($result));
		//echo $data['response']."==>".$result."<br>";
		}
	$data['oid']=$oid;
	return $data;
	}

public function checkQueryResult($id){
	$result="";
	
	$data['ak']="";
	$data['username']="";
	
	
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	$sql="SELECT * FROM oracle where oid=$id";
	$query = $db->query($sql);
	$row = $query->fetchArray();
	
	
	
	//print_r($row);
	$data=$row;//$this->object_array($row);
	
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	
	$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
	$results = $db->query($sql);

	while ($row = $results->fetchArray()) {
		$data['ak']=$row['ak'];
		$data['peerid']=$row['peerid'];
		$data['username']=$row['username'];
		$data['recommend']=$row['recommend'];
	}
	
	$ak=$data['ak'];
	$oracle_id=$data['oracle_id'];
	$oracle_query=$data['oracle_query'];
	
	
	$result=$this->getQueryResult($ak,$oracle_id,$oracle_query);
	
	$result=str_replace("or_","",$result);
	$data['response']=base64_decode(base64_decode($result));
	return $data;
	}

public function getQueryResult($ak,$oracle_id,$oracle_query){
	$url = PUBLIC_NODE."/v2/oracles/$oracle_id/queries";	
	$query=$this->getwebsrc($url);
	//echo "$ak=》$url<br/>QQ:$query";
	$nonce=$this->getNonce($ak)-1;
	//echo "Nonce:$nonce<br>";
	//echo "$query<br><br><br>";
	if(strpos($query,"response")>0){
		$info=json_decode($query);
		for($i=0;$i<count($info->oracle_queries);$i++){
			if($ak==$info->oracle_queries[$i]->sender_id ){
				$query=str_replace("ov_","",$info->oracle_queries[$i]->query);
				$query=$this->base64check_decode($query);
			//	echo "=>$query<=<br>$nonce=".$info->oracle_queries[$i]->sender_nonce;
				if($oracle_query==$query && $nonce==$info->oracle_queries[$i]->sender_nonce){
					return 	$info->oracle_queries[$i]->response;	
				}		
				}
			}
		}else{
			return "NULL";
			}
	return "NULL";
	}
	
//YWVfdXNkdBhudjg=	
public function base64check_decode($query){
	$newstr=substr(bin2hex(base64_decode($query)),0,strlen(bin2hex(base64_decode($query)))-8);
	return hex2bin($newstr);
	}		
public function postTx($tx,$pubnode){
	$jsonStr ='{ "tx": "'.$tx.'"}';
	return $this->http_post_json($pubnode, $jsonStr);
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
	}else{
		$url = $pubnode."/v2/transactions";
		}
	
	
	if($nonce==0){
		$nonce=$this->getNonce($sender_id);
	}
	$data['nonce']=$nonce;
	$ttl=0;
	$tx="";
	
	$cmd=realpath('.')."\\..\\env\\signtx.exe tx spend $sender_id $recipient_id $amount \"$payload\" --nonce $nonce --ttl $ttl --fee $gas";	
	exec($cmd,$ret);
	$tmpstr=explode(" ",$ret[7]);
	$tx= $tmpstr[count($tmpstr)-1];
	$cmd=realpath('.')."\\..\\env\\signtx.exe account sign ".realpath('.')."\\db\\$sender_id\\home $tx --password $password -n ae_mainnet";
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
	
	//echo $pubnode."==>".$jsonStr;


	$info=$this->http_post_json($url, $jsonStr);
	//var_dump($info);
	$return= json_decode(stripslashes($info[1])); 
	
	$url="No transaction";
	if(strpos($info[1],"tx_hash")>0){
		$tx_hash=$return->tx_hash;
		$url="<a href=https://www.aeknow.org/oracle/market/$tx_hash target=_blank>$tx_hash</a>";
		//echo $url;
	}else{
		echo $info;
		$url=$info;
		}

	return $url;
	}
	
public function queryOracle($id){
	$data['ak']="";
	$data['username']="";
	
	
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	$sql="SELECT * FROM oracle where oid=$id";
	$query = $db->query($sql);
	$row = $query->fetchArray();
	
	//print_r($row);
	$data=$row;//$this->object_array($row);
	
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	
	$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
	$results = $db->query($sql);

	while ($row = $results->fetchArray()) {
		$data['ak']=$row['ak'];
		$data['peerid']=$row['peerid'];
		$data['username']=$row['username'];
		$data['recommend']=$row['recommend'];
	}
	
$data['oid']=$id;
return $data;
	}
	
		
public function calcOracle($id){
	$data['ak']="";
	$data['username']="";
	
	
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	$sql="SELECT * FROM oracle where oid=$id";
	$query = $db->query($sql);
	$row = $query->fetchArray();
	
	//print_r($row);
	$data=$row;//$this->object_array($row);
	
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	
	$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
	$results = $db->query($sql);

	while ($row = $results->fetchArray()) {
		$data['ak']=$row['ak'];
		$data['peerid']=$row['peerid'];
		$data['username']=$row['username'];
		$data['recommend']=$row['recommend'];
	}
	
$data['predictstats']=$this->getPredictStats($id);
return $data;
	}

public function editOracle($id){
	$data['ak']="";
	$data['username']="";
	
	
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	$sql="SELECT * FROM oracle where oid=$id";
	$query = $db->query($sql);
	$row = $query->fetchArray();
	
	//print_r($row);
	$data=$row;//$this->object_array($row);
	
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	
	$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
	$results = $db->query($sql);

	while ($row = $results->fetchArray()) {
		$data['ak']=$row['ak'];
		$data['peerid']=$row['peerid'];
		$data['username']=$row['username'];
		$data['recommend']=$row['recommend'];
	}
	

return $data;
	}

public function viewOracle($id){
	$data['ak']="";
	$data['username']="";
	
	
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	$sql="SELECT * FROM oracle where oid=$id";
	$query = $db->query($sql);
	$row = $query->fetchArray();
	
	//print_r($row);
	$data=$row;//$this->object_array($row);
	
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	
	$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
	$results = $db->query($sql);

	while ($row = $results->fetchArray()) {
		$data['ak']=$row['ak'];
		$data['peerid']=$row['peerid'];
		$data['username']=$row['username'];
		$data['recommend']=$row['recommend'];
	}
	
	$data['id']=$id;
	$data['predictstats']="stats";
	$data['predictstats']=$this->getPredictStats($id);

return $data;

}



public function getPredictStats($id){
	$stats="";
	//get basic informations of the oracle
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	$sql="SELECT * FROM oracle where oid=$id";
	$query = $db->query($sql);
	$row = $query->fetchArray();
	//print_r($row);
	$oracle_json=$row['oracle_json'];
	$startheight=$row['startheight']-1;
	$endheight=$row['endheight'];
	$ak=$row['ak'];
	$returnrate=$row['returnrate'];
	
		
	$transaction_db=".\\db\\transaction.db";
	if(!file_exists($transaction_db)){
		$db = new SQLite3($transaction_db);
		$sql_init="create table transactions(tid INTEGER PRIMARY KEY,txtype text,txhash text,amount double,block_height double,sender_id text,recipient_id text,remark text);";
		if($db->exec($sql_init)){
			echo "Database for transactions=>created.<br>";
		}
	}
	
	$db = new SQLite3($transaction_db);
	$txstatus="";
	$sql="SELECT * FROM transactions WHERE block_height>$startheight AND block_height<$endheight order by block_height desc";
	$results = $db->query($sql);


	for($i=1;$i<11;$i++){
		$myoption[$i]=0;
		}
	
	while ($row = $results->fetchArray()) {
		if($ak==$row['recipient_id'] &&($row['amount']>0)){
			$amount=$row['amount']/1000000000000000000;
			$option=substr(sprintf("%.2f",$amount),0,-1);
			$select=$option-floor($option);
			//$count=$select*10;
			$count=intval(round($select*10));
			if($count==0){$count=10;}
			//$option[$count]=floatval($option[$count])+floatval($amount);
			$myoption[$count]=$myoption[$count]+$amount;
			//echo  $row['txhash']."=>$amount=>$option=>$select"."-$count<br>";
			//echo $row['txhash']."=>".($row['amount']/1000000000000000000)."<br>";
		}
	}
	
	for($i=1;$i<11;$i++){
		//echo $i."=>".$myoption[$i]."<br>";;
		}
	
	
	
	$info=json_decode($oracle_json);	
	//echo $oracle_json;
	//var_dump($info);
	$alltokens=0;
	$effectivetokens=0;
	//count total effective tokens
	for($i=0;$i<count($info->options);$i++){
		$option_init=$info->options[$i]->option_init;
		$option=$info->options[$i]->option;			
		$option_index=$info->options[$i]->index;
		//if($i!=9){$j=$i+1;}else{$j=0;}
		//$j=$i;
		
		if(trim($info->options[$i]->option)!=""){					
			$effectivetokens=$effectivetokens+$myoption[$option_index]+$option_init;			
			}
			
		$alltokens=$alltokens+$myoption[$option_index]+$option_init;	
		}
	
	$stats='<table  class="table table-hover">
					<tr>
					<th>#</th>
					<th>描述</th>
					<th>预投</th>
					<th>共投</th>
					<th>赔率</th>
					</tr>';
	
		
	for($i=0;$i<count($info->options);$i++){
		if(trim($info->options[$i]->option)!=""){
			$option=$info->options[$i]->option;
			$option_init=$info->options[$i]->option_init;
			$option_index=$info->options[$i]->index;
			
			//if($i!=9){$j=$i+1;}else{$j=0;}
			$prediction=$myoption[$option_index]+$option_init;
			if($prediction>0){
				$predictrate[$option_index]=round(((($effectivetokens)*$returnrate/100)/$prediction),2);
			}else{$predictrate[$option_index]=0;}
			//$stats.= "<li>$option /预投：$option_init/共投：$prediction/赔率:".round(((($alltokens-$prediction)*$returnrate/100)/$prediction),2)."</li>";
			$stats.="<tr><td>$option_index</td><td>$option</td><td>$option_init</td><td>$prediction</td><td>".$predictrate[$option_index]."</td></tr>";
			}
		}
	$stats.='</table>';
	$stats.="<br/> <b>有效token</b>:".$effectivetokens	."；<b>参与token</b>:".$alltokens		;
	return $stats;
	
	}

public function getFinishStats($id,$checkoption){
	
	$stats="";
	//get basic informations of the oracle
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	$sql="SELECT * FROM oracle where oid=$id";
	$query = $db->query($sql);
	$row = $query->fetchArray();
	//print_r($row);
	$oracle_json=$row['oracle_json'];
	$startheight=$row['startheight']-1;
	$endheight=$row['endheight'];
	$ak=$row['ak'];
	$returnrate=$row['returnrate'];
	
		
	$transaction_db=".\\db\\transaction.db";
	if(!file_exists($transaction_db)){
		$db = new SQLite3($transaction_db);
		$sql_init="create table transactions(tid INTEGER PRIMARY KEY,txtype text,txhash text,amount double,block_height double,sender_id text,recipient_id text,remark text);";
		if($db->exec($sql_init)){
			echo "Database for transactions=>created.<br>";
		}
	}
	
	$db = new SQLite3($transaction_db);
	$txstatus="";
	$sql="SELECT * FROM transactions WHERE block_height>$startheight AND block_height<$endheight order by block_height desc";
	$results = $db->query($sql);


	for($i=1;$i<11;$i++){
		$myoption[$i]=0;
		$predictrate[$i]=0;
		}
	$txs='<center><h1>详细账目</h1></center><table  class="table table-hover">
					<tr>
					<th>高度</th>
					<th>txhash</th>
					<th>账号</th>
					<th>共投</th>
					<th>赔率</th>
					<th>返还</th>
					</tr>';
	
	//获取交易分布
	while ($row = $results->fetchArray()) {
		if($ak==$row['recipient_id'] &&($row['amount']>0)){			
			$amount=$row['amount']/1000000000000000000;
			$option=substr(sprintf("%.2f",$amount),0,-1);
			$select=$option-floor($option);
			//$count=$select*10;	
			$count=intval(round($select*10));
			if($count==0){$count=10;}
			$myoption[$count]=$myoption[$count]+$amount;
		
		}
	}
	
	
	
	$info=json_decode($oracle_json);	
	$alltokens=0;
	$effectivetokens=0;
	//count total and effective tokens
	for($i=0;$i<count($info->options);$i++){
		$option_init=$info->options[$i]->option_init;
		$option=$info->options[$i]->option;	
		$option_index=$info->options[$i]->index;			
		//if($i!=9){$j=$i+1;}else{$j=0;}
		
		if(trim($info->options[$i]->option)!=""){			
			$effectivetokens=$effectivetokens+$myoption[$option_index]+$option_init;			
			}
			
		$alltokens=$alltokens+$myoption[$option_index]+$option_init;	
		}
	$stats=" <b>有效token</b>:".$effectivetokens	."；<b>参与token</b>:".$alltokens		;
	$stats.='<br/><br/><center><h1>预测结果</h1></center><table  class="table table-hover">
					<tr>
					<th>#</th>
					<th>描述</th>
					<th>预投</th>
					<th>共投</th>
					<th>赔率</th>
					</tr>';
	
		
	for($i=0;$i<count($info->options);$i++){
		if(trim($info->options[$i]->option)!=""){
			$option=$info->options[$i]->option;
			$option_init=$info->options[$i]->option_init;
			$option_index=$info->options[$i]->index;
			//if($i!=9){$j=$i+1;}else{$j=0;}
			//$j=$i;
			$prediction=$myoption[$option_index]+$option_init;
			$predictrate[$option_index]=round(((($effectivetokens)*$returnrate/100)/$prediction),2);
			//$stats.= "<li>$option /预投：$option_init/共投：$prediction/赔率:".round(((($alltokens-$prediction)*$returnrate/100)/$prediction),2)."</li>";
			if($checkoption==("option".$option_index)){
				$stats.="<tr style=\"background:green;color:white\"><td>$option_index</td><td>$option</td><td>$option_init</td><td>".$prediction."</td><td>".$predictrate[$option_index]."</td></tr>";
				}else{
				$stats.="<tr><td>$option_index</td><td>$option</td><td>$option_init</td><td>$prediction</td><td>".$predictrate[$option_index]."</td></tr>";	
				}			
			}
		}
	$stats.='</table>';
	
	
	
	///get lists
	
	while ($row = $results->fetchArray()) {
		if($ak==$row['recipient_id'] &&($row['amount']>0)){			
			$amount=$row['amount']/1000000000000000000;
			$option=substr(sprintf("%.2f",$amount),0,-1);
			//$option=(substr(sprintf("%.2f",$amount),0,-1)-floor($amount))*10;
			$select=$option-floor($option);
			$count=intval(round($select*10));//============================巨坑，填了三小时！=========================
			if($count==0){$count_index=10;}else{$count_index=$count;}
					
			
			
			$txhash="<a href=https://www.aeknow.org/block/transaction/".$row['txhash']." target=_blank>th_***".substr($row['txhash'],strlen($row['txhash'])-4,4)."</a>";
			$sender="<a href=https://www.aeknow.org/address/wallet/".$row['sender_id']." target=_blank>ak_***".substr($row['sender_id'],strlen($row['sender_id'])-4,4)."</a>";
					
			$returntokens=0;
			//echo $checkoption."=>".("option".$count)."=>$amount=>count_index:$count_index=>".$predictrate[$count_index]."<br />";
			
			if($checkoption==("option".$count_index)){
				$returntokens=$amount*$predictrate[$count_index];				
				$txs.="<tr style=\"background:green;color:white\"><td>".$row['block_height']."</td><td>$txhash</td><td>$sender</td><td>$amount</td><td>".$predictrate[$count_index]."</td><td>$returntokens</td></tr>";
			}else{				
				$thisrate=0;
				$txs.="<tr><td>".$row['block_height']."</td><td>$txhash</td><td>$sender</td><td>$amount</td><td>".$thisrate."</td><td>$returntokens</td></tr>";
				}		
		}
	}
	
	
	
	$txs.="</table>";
	
	$stats.=$txs;
	
	
	
	
	return $stats;
	
	}

public function updateOracle(){
	$this->input->post();
	$oid= $this->input->post('oid');
	$sender_id= $this->input->post('sender_id');
	$ak=$sender_id;
	$title= $this->input->post('title');
	$description= $this->input->post('description');
	$description=base64_encode($description);
	$returnrate= $this->input->post('returnrate');
	$oracle_id= $this->input->post('oracle_id');
	$oracle_query= $this->input->post('oracle_query');
	$startheight= $this->input->post('startheight');
	$endheight= $this->input->post('endheight');
	
	$isonchain= $this->input->post('isonchain');
	$onchainpassword= $this->input->post('onchainpassword');
	$onchainaddress= $this->input->post('onchainaddress');
	$onchainamount= $this->input->post('onchainamount');
	
	$option1= $this->input->post('option1');
	$option1_init= $this->input->post('option1_init');
	$option2= $this->input->post('option2');
	$option2_init= $this->input->post('option2_init');
	$option3= $this->input->post('option3');
	$option3_init= $this->input->post('option3_init');
	$option4= $this->input->post('option4');
	$option4_init= $this->input->post('option4_init');
	$option5= $this->input->post('option5');
	$option5_init= $this->input->post('option5_init');
	$option6= $this->input->post('option6');
	$option6_init= $this->input->post('option6_init');
	$option7= $this->input->post('option7');
	$option7_init= $this->input->post('option7_init');
	$option8= $this->input->post('option8');
	$option8_init= $this->input->post('option8_init');
	$option9= $this->input->post('option9');
	$option9_init= $this->input->post('option9_init');
	$option0= $this->input->post('option0');
	$option0_init= $this->input->post('option0_init');
	
	$payload= $this->input->post('payload');
	$isencrypt= $this->input->post('isencrypt');
	
	$oracle_json='
	{"title":"'.$title.'",
	"ak":"'.$sender_id.'",
	"description":"'.$description.'",
	"returnrate":'.$returnrate.',
	"oracle_id":"'.$oracle_id.'",
	"oracle_query":"'.$oracle_query.'",
	"startheight":'.$startheight.',
	"endheight":'.$endheight.',
	"options":[{"index":1,"option":"'.$option1.'","option_init":'.$option1_init.'},{"index":2,"option":"'.$option2.'","option_init":'.$option2_init.'},{"index":3,"option":"'.$option3.'","option_init":'.$option3_init.'},{"index":4,"option":"'.$option4.'","option_init":'.$option4_init.'},{"index":5,"option":"'.$option5.'","option_init":'.$option5_init.'},{"index":6,"option":"'.$option6.'","option_init":'.$option6_init.'},{"index":7,"option":"'.$option7.'","option_init":'.$option7_init.'},{"index":8,"option":"'.$option8.'","option_init":'.$option8_init.'},{"index":9,"option":"'.$option9.'","option_init":'.$option9_init.'},{"index":10,"option":"'.$option0.'","option_init":'.$option0_init.'}],
	"payload":"'.$payload.'"	
	}';
	
		$data['ak']="";
		$data['username']="";
		$dir_db=".\\db\\home.db";
		$db = new SQLite3($dir_db);
		
		$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
		$results = $db->query($sql);
	
		while ($row = $results->fetchArray()) {
			$data['ak']=$row['ak'];
			$data['peerid']=$row['peerid'];
			$data['username']=$row['username'];
			$data['recommend']=$row['recommend'];
		}
		
	
	
	if(trim($title)==""){echo "<h1>Title is required.</h1>"; exit;}
	
	
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	
	
	
	
	//$sql_insert="INSERT INTO oracle(ak,title,description,returnrate,oracle_id,	oracle_query,option1,option1_init,option2,option2_init,option3,option3_init,option4,option4_init,option5,option5_init,option6,option6_init,option7,option7_init,option8,option8_init,option9,option9_init,option0,option0_init,payload,oracle_json	) VALUES('$ak','$title','$description',$returnrate,'$oracle_id','$oracle_query','$option1',$option1_init,'$option2',$option2_init,'$option3',$option3_init,'$option4',$option4_init,'$option5',$option5_init,'$option6',$option6_init,'$option7',$option7_init,'$option8',$option8_init,'$option9',$option9_init,'$option0',$option0_init,'$payload','$oracle_json')";
	
	//echo "$sql_insert\n";
	
	$sql_update="UPDATE oracle set title='$title',description='$description',returnrate=$returnrate,oracle_id='$oracle_id',oracle_query='$oracle_query',startheight=$startheight,endheight=$endheight,option1='$option1',option1_init=$option1_init,option2='$option2',option2_init=$option2_init,option3='$option3',option3_init=$option3_init,option4='$option4',option4_init=$option4_init,option5='$option5',option5_init=$option5_init,	option6='$option6',option6_init=$option6_init,option7='$option7',option7_init=$option7_init,option8='$option8',option8_init=$option8_init,option9='$option9',option9_init=$option9_init,option0='$option0',option0_init=$option0_init,payload='$payload',oracle_json='$oracle_json' WHERE oid=$oid";
	$query_update =$db->query($sql_update);
	if($query_update){
		$data['status'] ="<h1>Oracle Updated</h1>";
	}
	
	$data['title']=$title;
	$data['oid']=$oid;
	
	$data['onchaintxhash']="Not onchain";
	if($isonchain=="Onchain" &&(trim($onchainpassword)!="")){
		$data['onchaintxhash']=$this->sendTx($sender_id,$onchainaddress,$onchainamount,$onchainpassword,base64_encode($oracle_json));
		}
		return $data;
	
	}


public function genOracle(){
	$this->input->post();
	$sender_id= $this->input->post('sender_id');
	$ak=$sender_id;
	$title= $this->input->post('title');
	$description= $this->input->post('description');
	$description=base64_encode($description);
	$returnrate= $this->input->post('returnrate');
	$oracle_id= $this->input->post('oracle_id');
	$oracle_query= $this->input->post('oracle_query');
	$startheight= $this->input->post('startheight');
	$endheight= $this->input->post('endheight');
	
	$isonchain= $this->input->post('isonchain');
	$onchainpassword= $this->input->post('onchainpassword');
	$onchainaddress= $this->input->post('onchainaddress');
	$onchainamount= $this->input->post('onchainamount');
	
	$option1= $this->input->post('option1');
	$option1_init= $this->input->post('option1_init');
	$option2= $this->input->post('option2');
	$option2_init= $this->input->post('option2_init');
	$option3= $this->input->post('option3');
	$option3_init= $this->input->post('option3_init');
	$option4= $this->input->post('option4');
	$option4_init= $this->input->post('option4_init');
	$option5= $this->input->post('option5');
	$option5_init= $this->input->post('option5_init');
	$option6= $this->input->post('option6');
	$option6_init= $this->input->post('option6_init');
	$option7= $this->input->post('option7');
	$option7_init= $this->input->post('option7_init');
	$option8= $this->input->post('option8');
	$option8_init= $this->input->post('option8_init');
	$option9= $this->input->post('option9');
	$option9_init= $this->input->post('option9_init');
	$option0= $this->input->post('option0');
	$option0_init= $this->input->post('option0_init');
	
	$payload= $this->input->post('payload');
	$isencrypt= $this->input->post('isencrypt');
	
	$oracle_json='
	{"title":"'.$title.'",
	"ak":"'.$sender_id.'",
	"description":"'.$description.'",
	"returnrate":'.$returnrate.',
	"oracle_id":"'.$oracle_id.'",
	"oracle_query":"'.$oracle_query.'",
	"startheight":'.$startheight.',
	"endheight":'.$endheight.',
	"options":[{"index":1,"option":"'.$option1.'","option_init":'.$option1_init.'},{"index":2,"option":"'.$option2.'","option_init":'.$option2_init.'},{"index":3,"option":"'.$option3.'","option_init":'.$option3_init.'},{"index":4,"option":"'.$option4.'","option_init":'.$option4_init.'},{"index":5,"option":"'.$option5.'","option_init":'.$option5_init.'},{"index":6,"option":"'.$option6.'","option_init":'.$option6_init.'},{"index":7,"option":"'.$option7.'","option_init":'.$option7_init.'},{"index":8,"option":"'.$option8.'","option_init":'.$option8_init.'},{"index":9,"option":"'.$option9.'","option_init":'.$option9_init.'},{"index":10,"option":"'.$option0.'","option_init":'.$option0_init.'}],
	"payload":"'.$payload.'"	
	}';
	
		$data['ak']="";
		$data['username']="";
		$dir_db=".\\db\\home.db";
		$db = new SQLite3($dir_db);
		
		$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
		$results = $db->query($sql);
	
		while ($row = $results->fetchArray()) {
			$data['ak']=$row['ak'];
			$data['peerid']=$row['peerid'];
			$data['username']=$row['username'];
			$data['recommend']=$row['recommend'];
		}
		
	
	
	if(trim($title)==""){echo "<h1>Title is required.</h1>"; exit;}
	
	
	$oracle_db=".\\db\\oracle.db";
	$db = new SQLite3($oracle_db);
	
	$sql="SELECT * FROM oracle WHERE title='$title'";
	$query =$db->query($sql);
	//$query = $this->db->query($sql);
	while( $row = $query->fetchArray()){
		echo "<h1>Title is Duplicated.</h1>"; exit;
		}
	
	
	$sql_insert="INSERT INTO oracle(ak,title,description,returnrate,oracle_id,oracle_query,startheight,endheight,option1,option1_init,option2,option2_init,option3,option3_init,option4,option4_init,option5,option5_init,option6,option6_init,option7,option7_init,option8,option8_init,option9,option9_init,option0,option0_init,payload,oracle_json) VALUES('$ak','$title','$description',$returnrate,'$oracle_id','$oracle_query',$startheight,$endheight,'$option1',$option1_init,'$option2',$option2_init,'$option3',$option3_init,'$option4',$option4_init,'$option5',$option5_init,'$option6',$option6_init,'$option7',$option7_init,'$option8',$option8_init,'$option9',$option9_init,'$option0',$option0_init,'$payload','$oracle_json')";
	//echo "$sql_insert\n";
	$oid=0;
	$query_insert =$db->query($sql_insert);
	if($query_insert){
		$data['status'] ="<h1>Oracle Inserted</h1>";
		$sql_getoid="SELECT oid FROM oracle ORDER BY oid desc limit 1";
		
		$results_getoid = $db->query($sql_getoid);
		
		while ($row = $results_getoid->fetchArray()) {
			$oid=$row['oid'];
		}
	}
	
	$data['title']=$title;
	$data['oid']=$oid;
	if($query_insert){
		//echo "Oracle inserted to db";
		}
		
	$data['onchaintxhash']="Not onchain";
	if($isonchain=="Onchain" &&(trim($onchainpassword)!="")){
		$data['onchaintxhash']=$this->sendTx($sender_id,$onchainaddress,$onchainamount,$onchainpassword,base64_encode($oracle_json));
		}
		
		
		return $data;
	
	}

public function showHome(){
		$data['ak']="";
		$data['username']="";
		$dir_db=".\\db\\home.db";
		$db = new SQLite3($dir_db);
		
		$sql="SELECT ak,peerid,username,recommend FROM system WHERE isdefault is TRUE limit 1";
		$results = $db->query($sql);
	
		while ($row = $results->fetchArray()) {
			$data['ak']=$row['ak'];
			$data['peerid']=$row['peerid'];
			$data['username']=$row['username'];
			$data['recommend']=$row['recommend'];
		}
		
		$oracle_db=".\\db\\oracle.db";
		if(!file_exists($oracle_db)){
			$db = new SQLite3($oracle_db);
			$sql_init="create table oracle(oid INTEGER PRIMARY KEY,ak text,title text,description text,returnrate double,oracle_id text,oracle_query text,startheight double,endheight double,option1 text,option1_init double,option2 text,option2_init double,option3 text,option3_init double,option4 text,option4_init double,option5 text,option5_init double,option6 text,option6_init double,option7 text,option7_init double,option8 text,option8_init double,option9 text,option9_init double,option0 text,option0_init double,payload text,oracle_json text, remark text);";
			if($db->exec($sql_init)){
				echo "Database =>created.<br>";
			}
		}
		
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
		$info=$this->getInfo($ak);
		$data['username']=$info['username'];
		$data['ak']=$data['id'];
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

public function getTxs($ak,$start,$end){
	
	//check db
	$transaction_db=".\\db\\transaction.db";
	if(!file_exists($transaction_db)){
		$db = new SQLite3($transaction_db);
		$sql_init="create table transactions(tid INTEGER PRIMARY KEY,txtype text,txhash text,amount double,block_height double,sender_id text,recipient_id text,remark text);";
		if($db->exec($sql_init)){
			echo "Database for transactions=>created.<br>";
		}
	}
	
	$db = new SQLite3($transaction_db);
	$txstatus="";
	
	if($end==0){$end=$this->GetTopHeight();}
	$url=PUBLIC_NODE."/v2/txbh/$ak/$start/$end";	
	$websrc=$this->getwebsrc($url);
	if(strpos($websrc,"txhash")>0){
		$txs=json_decode($websrc);
		for($i=0;$i<count($txs->txs);$i++){
			$txtype= $txs->txs[$i]->txtype;
			$txhash= $txs->txs[$i]->txhash;
			$amount= $txs->txs[$i]->amount;
			$block_height= $txs->txs[$i]->block_height;
			$sender_id= $txs->txs[$i]->sender_id;
			$recipient_id= $txs->txs[$i]->recipient_id;
			//$remark= $txs->txs[$i]->remark;
			
			$sql_check="SELECT * FROM transactions WHERE txhash='$txhash'";
			$results = $db->query($sql_check);
			$row = $results->fetchArray();
			//var_dump($row);
			if(!$row){
				$sql_insert="INSERT INTO transactions(txtype,txhash,amount,block_height,sender_id,recipient_id) VALUES('$txtype','$txhash',$amount,$block_height,'$sender_id','$recipient_id')";
				$results_insert = $db->query($sql_insert);
				//echo "$sql_insert<br />";
				$txstatus.= "$txhash ... is inserted into DB.<br />";
			}else{
				$txstatus.= "$txhash ... in DB.<br />";
				}
			}
	}
	
	
	
	$data['ak']="";
	$data['username']="";
	
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	$sql="SELECT ak,username FROM system WHERE ak='$ak'";
	$results = $db->query($sql);
	while ($row = $results->fetchArray()) {
		$data['ak']=$row[0];
		$data['username']=$row[1];
	}
	
	$data['txstatus']=$txstatus;
	
	return $data;
	}	
	
public function getInfo($ak){	
	$data['ak']="";
	$data['username']="";
	
	$dir_db=".\\db\\home.db";
	$db = new SQLite3($dir_db);
	$sql="SELECT ak,username FROM system WHERE ak='$ak'";
	$results = $db->query($sql);
	while ($row = $results->fetchArray()) {
		$data['ak']=$row[0];
		$data['username']=$row[1];
	}
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
	//echo "<br/>$url, $jsonStr<br/>";
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
	$url=PUBLIC_NODE."/v2/blocks/top";
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
