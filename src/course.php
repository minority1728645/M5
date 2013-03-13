<!DOCTYPE html>
<html>
    <head>
	<meta charset="UTF-8" />
        <title>Course Selection System</title>
        <link rel="shortcut icon" href="/images/m5.jpg"> 
        <link rel="stylesheet" type="text/css" href="css/style.css" />
 	    <link rel="stylesheet" type="text/css" href="css/gh-buttons.css" />
	    <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
		$('#admin').click(function(){
			$('#student').removeClass('active');
			$('#admin').addClass('active');	
			$("input:radio").val("admin");	
		});
		$('#student').click(function(){
			$('#admin').removeClass('active');
			$('#student').addClass('active');
			$("input:radio").val("student");		
		});
            });
        </script>
		<!--[if lte IE 7]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->
    </head>
    <body>
        <div class="container">
			<header>
				<h1>Course Selection System</h1>
				<h2>Create by team M5</h2>
				
				<div class="support-note">
					<span class="note-ie">Sorry, only modern browsers.</span>
				</div>
			</header>
			<section class="main">
				<form class="form-1" action='login.php' method='post'>
					<p class="field">
						<input type="text" name="username" placeholder="Username">
						<i class="icon-user icon-large"></i>
					</p>
						<p class="field">
							<input type="password" name="password" placeholder="Password">
							<i class="icon-lock icon-large"></i>
					</p>
					<div style="display: none;">
							<input type='radio' name='status' value='student' checked='checked'/>
            						<input type='radio' name='status' value='admin' />
					</div>
					<p class="submit">
						<button type="submit" name="submit"><i class="icon-arrow-right icon-large"></i></button>
					</p>
				</form>
				<center>
				<ul class="button-group">
    					<li><a id='student' class="button pill active">Student</a></li>
    					<li><a id='admin' class="button pill">Admin</a></li>
				</ul>
				</center>
			</section>
        </div>
    </body>
</html>
