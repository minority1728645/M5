<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    session_destroy();
}
else{
    $id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
    $con=mysql_connect('localhost','css','public');
    mysql_select_db('course_selection_system',$con);
    $result = mysql_query('select courses from student where id="'.$id.'"',$con);
    $row = mysql_fetch_assoc($result);
    $syllabus=array();
    if($row['courses'])
    {
        $ids=explode(' ',$row['courses']);
        foreach ($ids as $id){
            $result = mysql_query('select id,classid,name,teacher,time,place,num,max from course where id="'.$id.'"',$con);
            $row = mysql_fetch_assoc($result);
            array_push($syllabus,$row);
        }
    }
    mysql_close($con);
    echo json_encode($syllabus);
}
?>
