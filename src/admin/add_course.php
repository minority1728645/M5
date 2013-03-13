<?php session_start();
    if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
        session_destroy();
    }
    else{
        $classid=mysql_real_escape_string(filter_input(INPUT_POST,'classid'));
        $name=mysql_real_escape_string(filter_input(INPUT_POST,'name'));
        $teacher=mysql_real_escape_string(filter_input(INPUT_POST,'teacher'));
        $place=mysql_real_escape_string(filter_input(INPUT_POST,'place'));
        $time=mysql_real_escape_string(filter_input(INPUT_POST,'time'));
        $type=mysql_real_escape_string(filter_input(INPUT_POST,'type',FILTER_SANITIZE_NUMBER_INT));
        $max=mysql_real_escape_string(filter_input(INPUT_POST,'max',FILTER_SANITIZE_NUMBER_INT));
        $con=mysql_connect('localhost','css','public');
        mysql_select_db('course_selection_system',$con);
        $result = mysql_query('insert into course (classid,name,teacher,time,place,type,students,num,max) '.
            'values ("'.$classid.'","'.$name.'","'.$teacher.'","'.$time.'","'.$place.'",'.$type.',"","",'.$max.')');
        mysql_close($con);
    }
?>
