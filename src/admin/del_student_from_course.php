<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    session_destroy();
}
else{
        $id_course=mysql_real_escape_string($_POST['id_course']);
        $id_student=mysql_real_escape_string($_POST['id_student']);
        $con=mysql_connect('localhost','css','public');
        mysql_select_db('course_selection_system',$con);
        mysql_query('set autocommit=0',$con);
        $result=mysql_query('select courses from student where id="'.$id_student.'" for update',$con);
        $row=mysql_fetch_assoc($result);
        $former_courses=array_filter(explode(' ',$row['courses']));
        $courses=array_diff($former_courses,array($id_course));
        mysql_query('update student set courses="'.implode(' ',$courses).'" where id='.$id_student,$con);
    
        $result=mysql_query("select students,num from course where id='$id_course'",$con);
        $row=mysql_fetch_array($result);
        $students=array_filter(explode(' ',$row['students']));
        $students=array_diff($students,array($id_student));
        $num=$row['num'];
        if($num>0){$num=$num-1;}
        mysql_query('update course set num='.$num.',students="'.implode(' ',$students).'" where id="'.$id_course.'"',$con);
    
        mysql_query('commit',$con);
        mysql_close($con);
    
}
?>
