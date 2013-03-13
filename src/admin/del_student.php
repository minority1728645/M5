<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    session_destroy();
}
else{
    if(!$username=mysql_real_escape_string($_POST['username'])){
        echo 'invalid username';
    }
    else{
        $master=mysql_connect('localhost','css','public');
    
        mysql_select_db('course_selection_system',$master);
        mysql_query('set autocommit=0',$master);
        $result = mysql_query('select courses from student where username="'.$username.'" for update',$master);
        $row = mysql_fetch_array($result);
        $courses=array_filter(explode(' ',$row['courses']));

        $slave=mysql_connect('localhost','css','public');
        mysql_select_db('course_selection_system',$slave);

        foreach($courses as $course){
            mysql_query('set autocommit=0',$slave);
            $result=mysql_query("select students from course where id='$course'",$slave);
            $row=mysql_fetch_array($result);
            if($row){
                $students=array_filter(explode(' ',$row['students']));
                $students=array_diff($students,array($id));
                mysql_query('update course set num=num-1,students="'.implode(' ',$students).'" where id="'.$course.'"',$slave);
            }
            mysql_query('commit',$slave);
        }
        $success=array_diff($success,$return_success);
        print_r($return_success);

        mysql_close($slave);
        mysql_query('delete from student where username="'.$username.'"',$master);
        mysql_query('commit',$master);
        mysql_close($master);
    }
}
?>
