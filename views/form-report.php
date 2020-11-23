<link href="/layout/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="/layout/js/bootstrap.min.js"></script>
<script src="/layout/js/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="container">

<div class="form-sec">
  <br>
  <h4>Insira os parâmetros do relatório selecionado</h4>
  
 <form name="qryform" id="qryform" method="post" action="mail.php" onsubmit="return(validate());" novalidate="novalidate">
    <div class="form-group">
      <label>Name:</label>
      <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name">
    </div>	
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
  </div>


</div>