  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
       
        <li>
          <a href="/index.php" >
            <i class="fa fa-dashboard"></i> <span><?php echo $this->lang->line('info_mainpage');?></span>          
          </a>
        </li>
               
        <li><a href="/index.php/wallet/show/<?php echo $ak;?>" title="<?php echo $ak;?>"><i class="glyphicon glyphicon-credit-card"></i> <span><?php echo $this->lang->line('info_wallet');?></span></a></li>  
         <li><a href="/index.php/help/view/<?php echo "$ak/$mylang";?>"><i class="fa fa-book"></i> <span><?php echo $this->lang->line('info_help');?></span></a></li>
      </ul>
      
      
      
       <ul class="sidebar-menu" data-widget="tree">
		   <li class="header"> <center><h4>Links</h4></center></li>  
      <li>
          <a href="/index.php/wallet/other/<?php echo $ak;?>" >
            <i class="fa fa-dashboard"></i> <span><?php echo $this->lang->line('info_otherwallets');?></span>          
          </a>
        </li>
       </ul>  
        
    </section>
    <!-- /.sidebar -->
  </aside>
