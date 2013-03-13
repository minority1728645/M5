<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='student'){
    session_destroy();
}
else{
    $courses=array_unique(filter_var_array(array_filter(explode(' ',mysql_real_escape_string(filter_input(INPUT_POST,'courses')))),FILTER_VALIDATE_INT));
    $master=mysql_connect('localhost','css','public');

    mysql_select_db('course_selection_system',$master);

    $conflict_detect_array=array();
    foreach($courses as $course){
        $result = mysql_query('select time from course where id='.$course,$master);
        $row = mysql_fetch_array($result);
        $conflict_detect_array=array_merge($conflict_detect_array,explode(' ',$row[time]));
    }
    if(count($conflict_detect_array) != count(array_unique($conflict_detect_array))){
        echo '0';   
    }

    else{
        mysql_query('set autocommit=0',$master);
        $result = mysql_query('select courses from student where id="'.$_SESSION['id'].'" for update',$master);
        $row = mysql_fetch_array($result);
        $success=$former_courses=array_filter(explode(' ',$row['courses']));

        $slave=mysql_connect('localhost','css','public');
        mysql_select_db('course_selection_system',$slave);

        //return course
        $return_success=array();
        if($return_courses=array_diff($former_courses,$courses))
        {
            foreach($return_courses as $return_course){
                mysql_query('set autocommit=0',$slave);
                $result=mysql_query("select students from course where id='$return_course'",$slave);
                $row=mysql_fetch_array($result);
                if($row){
                    $students=array_filter(explode(' ',$row['students']));
                    $students=array_diff($students,array($_SESSION['id']));
                    mysql_query('update course set num=num-1,students="'.implode(' ',$students).'" where id="'.$return_course.'"',$slave);
                    $success=array_diff($success,array($return_course));
                    $result = mysql_query('select id,classid,name,teacher,time,place,type,num,max from course where id="'.$return_course.'"',$slave);
                    $row = mysql_fetch_assoc($result);
                    array_push($return_success,$row);
                }
                mysql_query('commit',$slave);
            }
        }

        //select course
        $select_success=array();
        if($select_courses=array_diff($courses,$former_courses))
        {
            foreach($select_courses as $select_course){
                mysql_query('set autocommit=0',$slave);
                $result=mysql_query("select students from course where id='$select_course' and num<max",$slave);
                $row=mysql_fetch_array($result);
                if($row){
                    $students=array_filter(explode(' ',$row['students']));
                    array_push($students,$_SESSION['id']);
                    mysql_query('update course set num=num+1,students="'.implode(' ',$students).'" where id="'.$select_course.'"',$slave);
                    array_push($success,$select_course);
                    $result = mysql_query('select id,classid,name,teacher,time,place,type,num,max from course where id="'.$select_course.'"',$slave);
                    $row = mysql_fetch_assoc($result);
                    array_push($select_success,$row);
                }
                mysql_query('commit',$slave);
            }
        }
        mysql_close($slave);
        mysql_query('update student set courses="'.implode(' ',$success).'" where id="'.$_SESSION['id'].'"',$master);
        mysql_query('commit',$master);
        mysql_close($master);
        echo json_encode(array('courses'=>$select_success,'return_courses'=>$return_success));
    }
}
?>
