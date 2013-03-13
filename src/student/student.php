<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='student'){
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
        <link rel="stylesheet" type="text/css" href="css/student_style.css" />
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
        var m_courses,sel_courses;
        var unfold_speed=200;
        var opened = false;
        var ELESUM=5;
        function pageChange(page){
            list_courses(page-1);
        }
        $(function(){		
            $('#cd-dropdown').dropdown({gutter:5,delay:100,random:true});
        });
        
        $(document).ready(function(){
            $("#submit").click(onSubmit);
            $('.cd-dropdown li').click(function(){
                var type=$(this).attr("data-value");
                $.post("/student/courses.php",{type:type},function(str){
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
            
            
            $.post("/student/syllabus.php",null,function(str){
                sel_courses=eval("("+str+")");
                clear_table();
                for (var i=0;i<sel_courses.courses.length;i++)
                    draw_course(i,true);
            });
            
            $("#time_table td").click(function(){
	            var str=$(this).children().html();
	            if (str=="") return;
	            var len=sel_courses.courses.length;
	            	for (var i=0;i<len;i++){
	            	    if (sel_courses.courses[i].name==str.split('<')[0]){
	            		    draw_course(i,false);
	            		    for (var k=i;k<len-1;k++) 
	            		    	sel_courses.courses[k]=sel_courses.courses[k+1];
	            		    sel_courses.courses.pop();
	            		    $.colorbox({transition:"none",html:"<div class='message'>退课成功...</div>"});
	            	}
	            }
            });
        });
        
        
        function onSubmit(){
        	var s="";
        	for (var i=0;i<sel_courses.courses.length;i++){
		        s+=sel_courses.courses[i].id;
		        if (i<sel_courses.courses.length) s+=" ";
	        }
        	$.post("/student/select.php",{courses:s},function(str){
		        sel_courses=eval("("+str+")");
		        var tm_str="";
		        if (sel_courses.courses.length>0){			
    		        $("#success table").html("<caption>以下课程选课成功</caption>"+
    		            "<tr><th class='first nobg'>课程ID</th><th class='nobg'>课程名</th><th class='nobg'>任课老师</th><th class='nobg'>上课时间</th><th class='nobg'>上课地点</th><th class='nobg'>已选人数</th><th class='nobg'>最大人数</th></tr>");
	    	        for (i=0;i<sel_courses.courses.length;i++){
	    	        	tm_str=cvt_to_str(sel_courses.courses[i].time,true);
	    	        	$("#success table").append("<tr>"+"<th>"+sel_courses.courses[i].classid+"</th>"+"<th>"+sel_courses.courses[i].name+"</th>"
	    	        	+"<th>"+sel_courses.courses[i].teacher+"</th>"+"<th>"+tm_str+"</th>"+"<th>"+sel_courses.courses[i].place+"</th>"+
	    	        	"<th>"+sel_courses.courses[i].num+"</th>"+"<th>"+sel_courses.courses[i].max+"</th>"+"</tr>");
	    	        }
	    	    }
	    	    else{
	    	        $("#success table").html("");
	    	    }
	    	    if (sel_courses.return_courses.length>0){
    		        $("#return table").html("<caption>以下课程退课成功</caption>"+
    		            "<tr><th class='first nobg'>课程ID</th><th class='nobg'>课程名</th><th class='nobg'>任课老师</th><th class='nobg'>上课时间</th><th class='nobg'>上课地点</th><th class='nobg'>已选人数</th><th class='nobg'>最大人数</th></tr>");
	    	        for (i=0;i<sel_courses.return_courses.length;i++){
	    	        	tm_str=cvt_to_str(sel_courses.return_courses[i].time,true);
	    	        	$("#return table").append("<tr>"+"<th>"+sel_courses.return_courses[i].classid+"</th>"	+"<th>"+sel_courses.return_courses[i].name+"</th>"
	    	        	+"<th>"+sel_courses.return_courses[i].teacher+"</th>"+"<th>"+tm_str+"</th>"+"<th>"+sel_courses.return_courses[i].place+"</th>"+
	    	        	"<th>"+sel_courses.return_courses[i].num+"</th>"+"<th>"+sel_courses.return_courses[i].max+"</th>"+"</tr>");
	    	        }
		        }
		        else{
		            $("#return table").html("");
		        }
		        if(sel_courses.courses.length==0 && sel_courses.return_courses.length==0){
		             $.colorbox({transition:"none",html:"<div class='message'>什么都没有发生...</div>"});
		        }
		        else{
		            $.colorbox({inline:true,href:"#inline_content"});
		        }
		        $.post("/student/syllabus.php",null,function(str){
		        	sel_courses=eval("("+str+")");
		        	clear_table();
		        	for (i=0;i<sel_courses.courses.length;i++)draw_course(i,true);
		        }); 
		    });
	    }

        
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
                	'<div class="title">Choose this course<a href="#" onclick="click_course('+(pages*ELESUM+i)+')" class="icon-link"></a></div>';
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
        
        function draw_course(i,isDraw)
        {
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
        function cvt_to_str(str,flag)
        {
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
        function click_course(i)
        {
	        if (!check_conflict(i)){
	        	$.colorbox({transition:"none",html:"<div class='message'>该时间段已有课程...</div>"});
	        	return;
	        }
	        else{
	            sel_courses.courses.push(m_courses.courses[i]);
	            draw_course(sel_courses.courses.length-1,true);
	            $.colorbox({transition:"none",html:"<div class='message'>选定成功...</div>"});
	        }
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
            <h1>Course Selection System<span>by team M5</span></h1>
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
            <div class="fright">
                <div class="fup">
                    <div class="fupleft">
                        <select id="cd-dropdown" name="cd-dropdown" class="cd-select">
                            <option value="-1" selected>Course type</option>
                            <option value="0" class="icon-monkey">必修</option>
                            <option value="1" class="icon-bear">通识</option>
                            <option value="2" class="icon-squirrel">任选</option>
                        </select>
                    </div>
                    <div class="fupright">
                        <p>
                        <div class="button-wrapper">
        					<a id="submit" class="a-btn" href="#inline_content">
    						<span class="a-btn-symbol">i</span>
    						<span class="a-btn-text">Submit</span> 
    						<span class="a-btn-slide-text">Submit result to server</span>
    						<span class="a-btn-slide-icon"></span>
        					</a>
                        </div>
                    </div>
                </div>
                <div class="fdown">                
                    <br /><br /><center>
                    <table id="time_table" cellspacing="0">
                        <tr>
                            <th scope="col" class="first nobg">SYB</th>
                            <th scope="col" class="nobg">Mon.</th>
                            <th scope="col" class="nobg">Tue.</th>
                            <th scope="col" class="nobg">Wed.</th>
                            <th scope="col" class="nobg">Thu.</th>
                            <th scope="col" class="nobg">Fri.</th>
                        </tr>
                        <tr>
                            <th scope="row" class="spec">08:00<br />10:00</th>
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        <tr>
                            <th scope="row" class="specalt">10:00<br />12:00</th>
                            <td class="alt"></td><td class="alt"></td><td class="alt"></td><td class="alt"></td><td class="alt"></td>
                            </tr>
                        <tr>
                            <th scope="row" class="spec">14:00<br />16:00</th>
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        <tr>
                            <th scope="row" class="specalt">16:00<br />18:00</th>
                            <td class="alt"></td><td class="alt"></td><td class="alt"></td><td class="alt"></td><td class="alt"></td>
                        </tr>
                    </table></center>
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
                <div id="return">
                    <table>
                    </table>
                </div>
			</div>
		</div>
    </body>
</html>
