<?php 
$this->config->set_item('language', $mylang); 
$this->lang->load('home');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Wallets - æpps of Aeternity - AEKnow</title>
  <Meta name="keywords" content="wallet,æpp,aepps,Aeternity,AEKnow">
  <Meta name="description" content="Lists of wallets, aepps of Aeternity">

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/static/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/static/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/static/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/static/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/static/dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="/static/bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/static/bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="/static/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/static/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  
    <!-- DataTables -->
  <link rel="stylesheet" href="/static/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/static/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<!-- Font as aeternity -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<?php $this->load->view('header');?> 
<?php $this->load->view('sidebar');?> 

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
           Wallets
        <small>Lists of wallets of Aeternity</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>      
        <li class="active"> wallets</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
<li>Waiting for you to be listed here... telegram: <a href="https://t.me/aeknow" target="_blank">https://t.me/aeknow</a> </li>

	 <div class="box-body table-responsive no-padding">
              <table id="example2" class="table table-bordered table-hover">
                <thead>				
                <tr>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Fisrt Release</th>
                  <th>License</th>
                  <th>Platform</th>
                  <th>Status</th>
                </tr>
                </thead>
                <tbody>
				
                
                 <tr>
                  <td> <a href="https://base.aepps.com">Base aepp</a></td>
                  <td>
					Base æpp is where you begin. Send & receive AE with the wallet. Create and manage dedicated accounts to safely store AE, to experiment and fuel æpps.<b>Mobile only</b>.
					 </td>
                  <td>2018-11-28</td>
                  <td>ISC</td>
                  <td>Android,iOS,Web</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr>  
                
               
                 <tr>
                  <td> <a href="https://airgap.it/">AirGap</a></td>
                  <td>
					With the AirGap two device approach secure key handling becomes more accessible. The AirGap Vault is installed on a dedicated or old smartphone that has no connection to any network, thus is air gapped. The AirGap Wallet is installed on a every-day smartphone.
					 </td>
                  <td>2018-11-28</td>
                  <td>not list</td>
                  <td>Android, iOS</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr>  
					
                               
                 
                 
                   <tr>
                  <td> <a href="https://www.anybit.io">Anybit</a></td>
                  <td>
					Your Mobile crypto Manager. Secure Friendly Fun.
					 </td>
                  <td>2019-01-12</td>
                  <td>Apache-2.0</td>
                  <td>Android, iOS</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr>  
                
                
                
               		
			
			<tr>
                  <td> <a href="https://t.me/aeternity">æternity Tip Bot</a></td>
                  <td>
				@AeternityTipBot: A simple æternity tipping bot. Message me privately or use /help /commands in a group we both are at.
					 </td>
                  <td>2019-01-23</td>
                  <td>Property</td>
                  <td>Telegram</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr>          
			
			 
                              
                 <tr>
                  <td> <a href="https://aenews.io/">TrustWallet</a></td>
                  <td>
			Online aetenrity wallet, Arkane.Network : Next generation blockchain wallet provider and developer api 
					 </td>
                  <td>2019-08-01</td>
                  <td>MIT</td>
                  <td>Android, iOS</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr> 
                
                
                <tr>
                  <td> <a href="https://aenews.io/">Arkane.network</a></td>
                  <td>
			Online aetenrity wallet, Arkane.Network : Next generation blockchain wallet provider and developer api 
					 </td>
                  <td>2019</td>
                  <td>not list</td>
                  <td>Web</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr> 
                
                 <tr>
                  <td> <a href="https://waellet.com/">Waellet</a></td>
                  <td>
			Waellet is a browser extension that allows you to interact with Aeternity blockchain in your browser.
					 </td>
                  <td>2019</td>
                  <td>ISC</td>
                  <td>Firefox,Chrome</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr> 
                
                
                <tr>
                  <td> <a href="https://github.com/jdgcs/aewallet">aeWallet</a></td>
                  <td>
			A standalone PC-based waellet, the transaction can be made OFFLINE or ONLINE. 
					 </td>
                  <td>2019</td>
                  <td>MIT</td>
                  <td>Windows, Linux</td>
                  <td><span class="badge bg-green">Active</span></td>
                </tr> 
                
               </tbody>
                <tfoot>
                <tr>
					
					

                  <th>Name</th>
                  <th>Description</th>
                  <th>Fisrt Release</th>
                  <th>License</th>
                  <th>Platform</th>
                  <th>Status</th>
                </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
          </div>

        <div class="col-md-9">

          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <?php $this->load->view('footer');?> 


</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="/static/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/static/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="/static/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="/static/bower_components/raphael/raphael.min.js"></script>
<script src="/static/bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="/static/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="/static/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/static/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="/static/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>

<!-- daterangepicker -->
<script src="/static/bower_components/moment/min/moment.min.js"></script>
<script src="/static/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="/static/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="/static/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="/static/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/static/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/static/dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="/static/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/static/dist/js/demo.js"></script>
<!-- DataTables -->
<script src="/static/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/static/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script>
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'paging'		: false,
      'autoWidth'   : false
    })
  })
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
</body>
</html>
