<?php include_once "inc/header.php";?>
<title>Meu Perfil</title>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<?php include_once "inc/navbar_topo.php";?>
<aside class="main-sidebar sidebar-dark-primary elevation-4"> 
<?php include_once "inc/sidebar_menu.php";?>
</aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<br/>
   <!-- <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Meu Perfil</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Início</a></li>
              <li class="breadcrumb-item active">Meu Perfil</li>
            </ol>
          </div>
        </div>
      </div>
    </section>-->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="dist/img/user4-128x128.jpg"
                       alt="">
                </div>

                <h3 class="profile-username text-center">Nina Mcintire</h3>

                <p class="text-muted text-center">Software Engineer</p>

             

                <a href="produtos.php" class="btn btn-default btn-block"><b><i class="fa fa-star"></i> Promover</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

    
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills" style="font-family:inter">
                 <!-- <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
                  -->
                  <li class="nav-item"><a class="nav-link active"  href="#settings" data-toggle="tab"><i class="fa fa-user"></i> Meu Perfil</a></li>
				  <li class="nav-item"><a class="nav-link" href="#login" data-toggle="tab"><i class="fa fa-key"></i> Login</a></li>
                </ul>
              </div><!-- /.card-header -->
              
			  
			  
			  
			  <div class="card-body">
                <div class="tab-content">
                 
				

                  <div class="active tab-pane" id="settings">
                    <form class="form-horizontal" style="font-family:inter; font-size:13px">
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control form-control-lg" id="inputName" placeholder="Nome">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">E-mail</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control form-control-lg" id="inputEmail" placeholder="E-mail">
                        </div>
                      </div>
               
                  
                      <div class="form-group row">
                        <label for="inputSkills" class="col-sm-2 col-form-label">Afiliação</label>
                        <div class="col-sm-10">
                          <input type="text" value="Afiliado" class="form-control form-control-lg" id="inputSkills" placeholder="Afiliação" disabled>
                        </div>
                      </div>
					  
					      <div class="form-group row">
                        <label for="inputExperience" class="col-sm-2 col-form-label">Box</label>
                        <div class="col-sm-10">
                          <textarea class="form-control form-control-lg" id="inputExperience" placeholder="Box"style="resize:none" ></textarea>
                        </div>
                      </div>
                
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-success">Editar <i class="fa fa-angle-right"></i></button>
                        </div>
                      </div>
                    </form>
                  </div>
				  
				  
				       <div class="tab-pane" id="login">
                    <form class="form-horizontal" style="font-family:inter; font-size:13px">
               
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">E-mail*</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control form-control-lg" id="inputEmail" placeholder="@e-mail" required >
                        </div>
                      </div>
					  
					         <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Senha*</label>
                        <div class="col-sm-10">
                          <input type="password" class="form-control form-control-lg" id="inputName" placeholder="....." required >
                        </div>
                      </div>
					  
                  
                
                
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-success">Editar <i class="fa fa-angle-right"></i></button>
                        </div>
                      </div>
                    </form>
                  </div>
				  
				  
				  
				  
				  
				  
				  
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
   

</body>
<?php include_once "inc/footer.php";?>