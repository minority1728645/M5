<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    session_destroy();
}
else{
        $id_course=mysql_real_escape_string($_POST['id_course']);
        $username=mysql_real_escape_string($_POST['username']);
        $con=mysql_connect('localhost','css','public');
        mysql_select_db('course_selection_system',$con);
        $result=mysql_query('select id from student where username="'.$username.'"',$con);
        $row=mysql_fetch_assoc($result);
        $id_student=$row['id'];
        mysql_query('set autocommit=0',$con);
        $result=mysql_query('select courses from student where id="'.$id_student.'" for update',$con);
        $row=mysql_fetch_assoc($result);
        $courses=array_filter(explode(' ',$row['courses']));
        array_push($courses,$id_course);
        mysql_query('update student set courses="'.implode(' ',$courses).'" where id='.$id_student,$con);
        
        $result=mysql_query("select students from course where id='$id_course'",$con);
        $row=mysql_fetch_array($result);
        $students=array_filter(explode(' ',$row['students']));
        array_push($students,$id_student);
        mysql_query('update course set num=num+1,students="'.implode(' ',$students).'" where id="'.$id_course.'"',$con);

        mysql_query('commit',$con);
        mysql_close($con);
}
?>
