<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    session_destroy();
}
else{
    if(!$classid=mysql_real_escape_string($_POST['classid'])){
        echo 'invalid id';
    }
    else{
        $master=mysql_connect('localhost','css','public');
        mysql_select_db('course_selection_system',$master);
        $result = mysql_query('select id from course where classid="'.$classid.'"',$master);
        $row = mysql_fetch_array($result);
        $id=$row['id'];
        mysql_query('set autocommit=0',$master);
        $result = mysql_query('select students from course where id="'.$id.'" for update',$master);
        $row = mysql_fetch_array($result);
        $students=array_filter(explode(' ',$row['students']));
        mysql_query('delete from course where id='.$id,$master);
        mysql_query('commit',$master);
        mysql_close($master);
        if($students)
        {
            $slave=mysql_connect('localhost','css','public');
            mysql_select_db('course_selection_system',$slave);
            foreach($students as $student){
                mysql_query('set autocommit=0',$slave);
                $result=mysql_query("select courses from student where id='$student' for update",$slave);
                $row=mysql_fetch_array($result);
                if($row){
                    $former_courses=array_filter(explode(' ',$row['courses']));
                    $courses=array_diff($former_courses,array($id));
                    mysql_query('update student set courses="'.implode(' ',$courses).'" where id='.$student,$slave);
                }
                mysql_query('commit',$slave);
            }
            mysql_close($slave);
        }
        echo 'delete success';
    }
}
?>
