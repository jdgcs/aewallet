<?php 
$this->config->set_item('language', $mylang); 
$this->lang->load('home');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Wallet</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/static/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/static/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/static/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/static/dist/css/AdminLTE.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
 <link rel="stylesheet" href="/static/dist/css/skins/skin.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

 <?php $this->load->view('header'); ?>
  <!-- Left side column. contains the sidebar -->
 <?php $this->load->view('sidebar'); ?>
  <!-- =============================================== -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Wallet
        <small><?php echo $ak;?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">wallet</a></li>
        <li class="active">aeternity</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
		<form class="form-horizontal" action="/index.php/wallet/posttx" method="POST">		
			
		 <div class="col-md-8">
          <!-- Profile Image -->
          <div class="box box-primary">
			  <div class="box-header with-border">
              <h3 class="box-title">Signed TX</h3>
            </div>
            <div class="box-body box-profile">	
			
				<div class="col-md-12">
				<textarea  rows="12"  class="form-control" name="tx" readonly="readonly"  style="width: 100%"><?php echo $tx;?></textarea>
				 <input type="hidden" class="form-control" name="pubnode"  value="<?php echo $pubnode;?>" readonly="readonly"> 
				</div>              
		
            </div>            
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

        </div>
        
        
         <div class="col-md-4">
         
          <!-- Profile Image -->
          <div class="box box-primary">
			  <div class="box-header with-border">              
              <?php echo $this->lang->line('info_offlinescan');?>
            </div>
            <div class="box-body box-profile">	
						          
				<div class="col-md-4">				
				<img class="" style="with:220px;height:220px;" src="/index.php/wallet/getimg/<?php echo urlencode("https://www.aeknow.org/v2/gettx/".urlencode($tx));?>" alt="User profile picture">
				</div>
				<br /><br />
            </div>
            
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
         </div>
	
        
        <!-- /.col -->
       <div class="col-md-12">
       <button type="submit"  class="btn btn-primary btn-block"  style="with:200px;"><b><?php echo $this->lang->line('info_posttx');?></b></button>
       </div> 
      
       <br/> <br/>
              
       <div class="col-md-12">
		<!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" >
              <div class="box-body">
				   <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('info_senderaddress');?></label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="sender_id" value="<?php echo $sender_id;?>" readonly="readonly" >
                  </div>
                </div> 
                
				 </form>
				   
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('info_recipientaddress');?></label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="recipient_id" value="<?php echo $recipient_id;?>" readonly="readonly" >
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('info_amount');?></label>
                  <div class="col-sm-10">
					  <div class="input-group">
                    <input type="text" class="form-control" name="amount" value="<?php echo $amount;?>" readonly="readonly" > 
                    <span class="input-group-addon">
                     AE 
                     </span>
                     </div>
                  </div>
                  
            
                </div>
                 <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Payload</label>
                  <div class="col-sm-10">
                    <div class="input-group">
                        
                    <input type="text" class="form-control" name="payload"  value="<?php echo $payload;?>" readonly="readonly" >
                    <span class="input-group-addon">
                          Encrypt &nbsp;<input type="checkbox" name="isencrypt" disabled="disabled" >
                     </span>
                  </div>
                  </div>
                </div>
             
              <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Tx Hash</label>
                  <div class="col-sm-10">       
                    <input type="text" class="form-control" name="payload"  value="<?php echo $txhash;?>" readonly="readonly" >
                  </div>
                </div>
             
                            
               
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('info_gas');?></label>
                  <div class="col-sm-10">
					  <div class="input-group">
                    <input type="email" class="form-control" name="gas" value="<?php echo $gas;?>" readonly="readonly" > 
                    <span class="input-group-addon">
                      AE
                     </span>
                     </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Nonce</label>
                  <div class="col-sm-10">
					
                    <input type="email" class="form-control" name="nonce"  value="<?php echo $nonce;?>" readonly="readonly"> 
                    
                  </div>
                </div>
             
             <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('info_node');?></label>
                  <div class="col-sm-10">
					
                    <input type="text" class="form-control" name="pubnode"  value="<?php echo $pubnode;?>" readonly="readonly"> 
                    
                  </div>
                </div>
                
                
		
		</div>
       
       
        </div>
        
        <div class="box-body">
			
             </div>
              <!-- /.box-body -->
              <div class="box-footer">
               
              </div>
              <!-- /.box-footer -->
            </form>
          </div>
          <!-- /.box -->
        
       </div>
        <!-- /.box-body -->
        <div class="box-footer">
       
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('footer'); ?>

  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="/static/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/static/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="/static/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/static/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/static/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/static/dist/js/demo.js"></script>
<script>
  $(document).ready(function () {
    $('.sidebar-menu').tree()
  })
</script>
</body>
</html>
