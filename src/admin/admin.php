<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    header('location: ../course.php');
    session_destroy();
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <title>Course Selection System</title>
        <link rel="shortcut icon" href="/images/m5.jpg"> 
        <link rel="stylesheet" type="text/css" href="css/admin_style.css" />
        <link rel="stylesheet" type="text/css" href="css/pfold.css" />
        <link rel="stylesheet" type="text/css" href="css/custom2.css" />		
        <link rel="stylesheet" type="text/css" href="css/page_style.css" />
        <link rel="stylesheet" type="text/css" href="css/btn_style.css" />
		<link rel="stylesheet" type="text/css" href="css/colorbox.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/modernizr.custom.1.js"></script>
        <script type="text/javascript" src="js/modernizr.custom.2.js"></script> 
        <script type="text/javascript" src="js/jquery.dropdown.js"></script>
        <script type="text/javascript" src="js/jquery.pfold.js"></script>
		<script type="text/javascript" src="js/jquery.paginate.js"></script>
		<script type="text/javascript" src="js/jquery.colorbox.js"></script>
        <script type="text/javascript">
        var m_courses,sel_courses,name_list;
        var unfold_speed=200;
        var opened = false;
        var ELESUM=4;
        function pageChange(page){
            list_courses(page-1);
        }
        $(function(){		
            $('#cd-dropdown').dropdown({gutter:5,delay:100,random:true});
        });
        
        $(document).ready(function(){
            $('.cd-dropdown li').click(function(){
                var type=$(this).attr("data-value");
                $.post("/admin/courses.php",{type:type},function(str){
		            $('#grid').empty();
            		m_courses=eval("("+str+")");
            		str="";
            		var page_num=(m_courses.courses.length-1) / ELESUM | 0 ;
                		list_courses(0);
		                	$("#paginate").paginate({
		                		count 	: page_num+1,
		                		start 	: 1,
		                		display  : 3,
		                		border	: false,
		                		text_color  : '#79B5E3',
		                		background_color   : 'none',	
		                		text_hover_color  : '#2573AF',
		                		background_hover_color	: 'none', 
		                		images	: false,
		                		mouse	: 'press',
		                		onChange : pageChange
		                	});
                });
            });
            
            
            $.post("/admin/syllabus.php",null,function(str){
                sel_courses=eval("("+str+")");
                clear_table();
                for (var i=0;i<sel_courses.courses.length;i++)
                    draw_course(i,true);
            });
            
        });

        
        function list_courses(pages){
            $('#grid').empty();
            for (var i=0;i<ELESUM &&i<(m_courses.courses.length-pages*ELESUM);i++){
                var tm_str=cvt_to_str(m_courses.courses[pages*ELESUM+i].time,true);
                var tipContent ='<p><b>课程ID : </b>'+m_courses.courses[pages*ELESUM+i].classid+'<br />' +
                	'<b>课程名  : </b>'+m_courses.courses[pages*ELESUM+i].name+'<br />' +
                    '<b>任课教师 : </b>'+m_courses.courses[pages*ELESUM+i].teacher+'<br />' +
                    '<b>上课时间 : </b><br />'+tm_str+'<br />' +
                    '<b>上课地点 : </b>'+m_courses.courses[pages*ELESUM+i].place+'<br />' +
                	'<b>已选人数 : </b>'+m_courses.courses[pages*ELESUM+i].num+'<br />' +
                	'<b>最大人数 : </b>'+m_courses.courses[pages*ELESUM+i].max+'<br /></p>'+
                	'<div class="title">Add/Remove Students<a href="#" onclick="add_student('+(pages*ELESUM+i)+')" class="icon-link-l">Ad</a><a href="#" onclick="remove_student('+(pages*ELESUM+i)+')" class="icon-link">Rm</a></div>';
                $('#grid').append(
                    '<div class="uc-container">'+
                          '<div class="uc-initial-content"><p><center>'+m_courses.courses[pages*ELESUM+i].name+'</center></p>'+
                                '<span class="icon-eye"></span>'+
                          '</div>'+
                          '<div class="uc-final-content">'+
                                '<p><center>'+tipContent+'</center></p>'+
                                '<span class="icon-cancel"></span>'+
                          '</div>'+
                    '</div>'
                );
            }
                    opened = false;
                    $( '#grid > div.uc-container' ).each( function( i ) {
                        var $item = $( this ), direction;
                        var pfold = $item.pfold( {
                                folddirection : direction,
                                speed : unfold_speed,
                                onEndFolding : function() { opened = false; },
                                centered : false
                        } );
                        $item.find( 'span.icon-eye' ).on( 'click', function() {
                            if( !opened ) {
                                opened = true;
                                pfold.unfold();
                            }}).end().find( 'span.icon-cancel' ).on( 'click', function() {
                            pfold.fold();
                        });
                        $item.find( 'a.icon-link' ).on( 'click', function() {
                            pfold.fold();
                        });
                    });	
        }
        
        function draw_course(i,isDraw){
            var str="";
            if (isDraw==true) {
                str=sel_courses.courses[i].name;
                str+=("<br />"+sel_courses.courses[i].place);
            }
            var tm=new Array();
            var obj;
            tm=sel_courses.courses[i].time.split(" ");
            for (var k=0;k<tm.length;k++){
                obj=$("#time_table tr:eq("+(parseInt(tm[k].split(",")[1])+1)+") td:nth-child("+(parseInt(tm[k].split(",")[0])+2)+")");
                if(isDraw) obj.html("<a href='#'>"+str+"</a>");
                else obj.html("");
            }
        }
        function cvt_to_str(str,flag){
        	var week=new Array("星期一","星期二","星期三","星期四","星期五");
        	var time=new Array("08:00-10:00","10:00-12.00","14:00-16:00","16:00-18:00");
        	var tm=new Array();
        	tm=str.split(" ");
        	var tm_str="";
        	for (var j=0;j<tm.length;j++){
        		tm_str+=week[parseInt(tm[j].split(",")[0])]+time[parseInt(tm[j].split(",")[1])];
        		if (j<tm.length-1){
        			if (flag) tm_str+='<br />';
        		}
        	}
        	return tm_str;
        }
        function check_conflict(i)
        {
        	var tm=new Array();
        	tm=m_courses.courses[i].time.split(" ");
        	for (var k=0;k<tm.length;k++){
        		var obj=$("#time_table tr:eq("+(parseInt(tm[k].split(",")[1])+1)+") td:nth-child("+(parseInt(tm[k].split(",")[0])+2)+")");
        		if (obj.html()!="") return false;
        	}
        	return true;
        }
        function remove_student(i)
        {
            var course_id=m_courses.courses[i].id;
	        $.post("/admin/name_list.php",{id:course_id},function(str){
		        name_list=eval("("+str+")");
		        if (name_list.length>0){			
    		        $("#success table").html("<caption>学生名单</caption>"+
    		            "<tr><th>学生姓名</th><th>学生用户名</th></tr>");
	    	        for (i=0;i<name_list.length;i++){
	    	        	$("#success table").append("<tr>"+"<td><a href='#'  onclick='remove_student_from_course("+course_id+","+name_list[i].id+")'>"+name_list[i].name+"</a></td>"	
	    	        	+"<td>"+name_list[i].username+"</td>"+"</tr>");
	    	        }
		          $.colorbox({inline:true,href:"#inline_content"});
	    	    }
	    	    else{
	    	        $("#success table").html("");
	    	        $.colorbox({transition:"none",html:"<div class='message'>该课程暂无学生...</div>"});
	    	    }
		      });
        }
        function remove_student_from_course(course_id,student_id){
            $.post("/admin/del_student_from_course.php",{'id_course': course_id ,'id_student':student_id},function(){
                var type=$('.cd-dropdown li').attr("data-value");
                $.post("/admin/courses.php",{type:type},function(str){
                    $('#grid').empty();
                    m_courses=eval("("+str+")");
                    str="";
                    var page_num=(m_courses.courses.length-1) / ELESUM | 0 ;
                        list_courses(0);
                            $("#paginate").paginate({
                                count   : page_num+1,
                                start   : 1,
                                display  : 3,
                                border  : false,
                                text_color  : '#79B5E3',
                                background_color   : 'none',    
                                text_hover_color  : '#2573AF',
                                background_hover_color  : 'none', 
                                images  : false,
                                mouse   : 'press',
                                onChange : pageChange
                            });
                });
                $.colorbox.close();
            });
        }
        function add_student(i){
            var course_id=m_courses.courses[i].id;
            $.colorbox({transition:"none",html:"<h2>Type his username:</h2><br /> <input id='username' type='text' name='username' /><button type='button'  style='float:right' onclick='add_student_from_course("+course_id+")'>Submit</button>"});
        }
        function add_student_from_course(course_id){
            var user=$("#username").val();
            $.post("/admin/add_student_into_course.php",{'id_course': course_id ,'username':user},function(){
                flush_course();
                $.colorbox.close();
            });
        }
        function flush_course(){
            var type=$('.cd-dropdown li').attr("data-value");
            $.post("/admin/courses.php",{type:type},function(str){
                    $('#grid').empty();
                    m_courses=eval("("+str+")");
                    str="";
                    var page_num=(m_courses.courses.length-1) / ELESUM | 0 ;
                        list_courses(0);
                            $("#paginate").paginate({
                                count   : page_num+1,
                                start   : 1,
                                display  : 3,
                                border  : false,
                                text_color  : '#79B5E3',
                                background_color   : 'none',    
                                text_hover_color  : '#2573AF',
                                background_hover_color  : 'none', 
                                images  : false,
                                mouse   : 'press',
                                onChange : pageChange
                            });
                });
        }
        function add_a_student(){
            $.colorbox({transition:"none",html:"<div class='addst'><h2>Username: </h2>"+
                "<input type='text' size='15' /><h2>Password: </h2><input type='password' size='15' />"+
                "<h2>Name: </h2><input type='text' size='15' />"+
                "<br /><br /><br /><button type='button' style='float:right' onclick='_add_a_student()'>Submit</button></div>"});
        }
        function _add_a_student(){
            var username=$(".addst input:eq(0)").val();
            var password=$(".addst input:eq(1)").val();
            var name=$(".addst input:eq(2)").val();
            $.post("/admin/add_student.php",{'username': username ,'password':password,'name':name},function(){
                $.colorbox.close();
            });
        }
        function del_a_student(){
            $.colorbox({transition:"none",html:"<div class='delst'><h2>Username: </h2>"+
                "<input type='text' size='15'/>"+
                "<br /><br /><br /><button type='button' style='float:right' onclick='_del_a_student()'>Submit</button></div>"});
        }
        function _del_a_student(){
            var username=$(".delst input:eq(0)").val();
            $.post("/admin/del_student.php",{'username': username},function(){
                $.colorbox.close();
            });
        }
        function add_a_course(){
            $.colorbox({transition:"none",html:"<div class='addcs'><h2>ClassID: </h2>"+
                "<input type='text' size='15' /><h2>Name: </h2><input type='text'size='15' /><h2>Teacher:</h2>"+
                "<input type='text' size='15' /><h2>Time:</h2><input type='text' size='15' /><h2>Place:</h2>"+
                "<input type='text' size='15' /><h2>Max students:</h2><input type='text' size='15' />"+
                "<br /><br /><br /><h2>Type:<select><option value='0'>必修课</option><option value='1'>通识课</option>"+
                "<option value='2'>任选课</option></select>"+
                "<button type='button' style='float:right' onclick='_add_a_course()'>Submit</button></h2></div>"});
        }
        function _add_a_course(){
            var classid=$(".addcs input:eq(0)").val();
            var name=$(".addcs input:eq(1)").val();
            var teacher=$(".addcs input:eq(2)").val();
            var time=$(".addcs input:eq(3)").val();
            var place=$(".addcs input:eq(4)").val();
            var max=$(".addcs input:eq(5)").val();
            var type=$(".addcs select").val();
            $.post("/admin/add_course.php",{'classid':classid,'name':name,'teacher':teacher,'time':time,'place':place,
                'max':max,'type':type,},function(){
                flush_course();
                $.colorbox.close();
            });
        }
        function del_a_course(){
            $.colorbox({transition:"none",html:"<div class='delcs'><h2>ClassID: </h2>"+
                "<input type='text' size='15' />"+
                "<br /><br /><br /><button type='button' style='float:right' onclick='_del_a_course()'>Submit</button></div>"});
        }
        function _del_a_course(){
            var classid=$(".delcs input:eq(0)").val();
            $.post("/admin/del_course.php",{'classid': classid},function(){
                flush_course();
                $.colorbox.close();
            });
        }
        function clear_table(){
        	var a=$("table td");
        	for(var i=0;i<a.length ; i++)
        		$(a[i]).html("");
        }
        </script>
    </head>
    <body>
        <div class="container">			
            <header class="clearfix">			
            <h1>Course Selection System(Admin)<span>by team M5</span></h1>
            <nav class="codrops-demos">
            <a class="current-demo" href="/logout.php">Logout</a>
            </nav>				
            </header>
            <section class="main clearfix">
            <div class="fleft">
                   <h2>&nbsp;&nbsp;&nbsp;&nbsp;Click for details</h2>
                    <div id="grid" class="grid demo-2">
                    <p><p>
                    <p><h3>&nbsp;&nbsp;&nbsp;Choose course type first!<h3></p>
                    </div><!-- / grid -->
                    <div id="paginate">                   
                    </div>
            </div>
            <div class="fmiddle">
                <br /><br /><br /><br />
                <select id="cd-dropdown" name="cd-dropdown" class="cd-select">
                    <option value="-1" selected>Course type</option>
                    <option value="0" class="icon-monkey">必修</option>
                    <option value="1" class="icon-bear">通识</option>
                    <option value="2" class="icon-squirrel">任选</option>
                </select>
            </div>
            <div class="fright">   
                <h2 style='float:right'>General Operations&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>    
                <div class="button-wrapper" style='float:right'>
                    <a id="del_student" class="a-btn" href="#" onclick="del_a_student()">
                    <span class="a-btn-symbol">i</span>
                    <span class="a-btn-text">Del Student</span> 
                    <span class="a-btn-slide-text">delete a student</span>
                    <span class="a-btn-slide-icon"></span>
                    </a>
                </div>
                <div class="button-wrapper" style='float:right'>
                    <a id="del_course" class="a-btn" href="#" onclick="del_a_course()">
                    <span class="a-btn-symbol">i</span>
                    <span class="a-btn-text">Del Course</span> 
                    <span class="a-btn-slide-text">delete a course</span>
                    <span class="a-btn-slide-icon"></span>
                    </a>
                </div>
                <div class="button-wrapper" style='float:right'>
                    <a id="add_student" class="a-btn" href="#" onclick="add_a_student()">
                    <span class="a-btn-symbol">i</span>
                    <span class="a-btn-text">Add Student</span> 
                    <span class="a-btn-slide-text">add a student</span>
                    <span class="a-btn-slide-icon"></span>
                    </a>
                </div>
                <div class="button-wrapper" style='float:right'>
                    <a id="add_course" class="a-btn" href="#" onclick="add_a_course()">
                    <span class="a-btn-symbol">i</span>
                    <span class="a-btn-text">Add Course</span> 
                    <span class="a-btn-slide-text">add a course</span>
                    <span class="a-btn-slide-icon"></span>
                    </a>
                </div>
            </div>
            </section>
        </div><!-- /container -->
        <div style='display:none'>
			<div id='inline_content' style='padding:10px; background:#fff;'>
			    <div id="success">
                	<table>
                	</table>
                </div>
			</div>
		</div>
    </body>
</html>
