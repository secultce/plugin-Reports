<link href="/layout/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="/layout/js/bootstrap.min.js"></script>
<script src="/layout/js/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="container">

<div class="form-sec">
  <h4>Contact form</h4>
  
 <form name="qryform" id="qryform" method="post" action="mail.php" onsubmit="return(validate());" novalidate="novalidate">
    <div class="form-group">
      <label>Name:</label>
      <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name">
    </div>
    <div class="form-group">
      <label>Email:</label>
      <input type="email" class="form-control" id="name" placeholder="Enter Email" name="email">
    </div>
    
    <div class="form-group">
      <label>Phone No.:</label>
      <input type="text" class="form-control" id="phone" placeholder="Enter Phone no." name="phone">
    </div>
	<div class="form-group">
      <label>Subject:</label>
      <input type="text" class="form-control" id="name" placeholder="Subject" name="subject">
    </div>
	 
	<div class="form-group">
      <label>Issues/query:</label>
      <textarea name="issues" class="form-control" id="iq" placeholder="Enter your Issues/query"></textarea>
    </div>
	
    
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
  </div>


</div>