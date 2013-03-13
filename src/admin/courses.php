<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    session_destroy();
}
else{
    $type=mysql_real_escape_string(filter_input(INPUT_POST,'type',FILTER_VALIDATE_INT));
    $con=mysql_connect('localhost','css','public');
    mysql_select_db('course_selection_system',$con);
    $result = mysql_query('select id,classid,name,teacher,time,place,type,num,max from course where type="'.$type.'"',$con);
    //$result = mysql_query('select id,classid,name,teacher,time,place,type,num,max from course',$con);
    $courses=array();
    while($row = mysql_fetch_assoc($result)){
        array_push($courses,$row);
    }
    echo json_encode(array('courses'=>$courses));
    mysql_close($con);
}

?>
