<?php session_start();
function AdminValidate($username,$password){
    $con=mysql_connect('localhost','css','public');
    mysql_select_db('course_selection_system',$con);
    $result = mysql_query("select id from admin where username='$username' and password='$password'",$con);
    $row = mysql_fetch_array($result);
    mysql_close($con);
    if(!$row['id']) return false;
    else return $row['id'];
}
function StudentValidate($username,$password){
    $con=mysql_connect('localhost','css','public');
    mysql_select_db('course_selection_system',$con);
    $result = mysql_query("select id from student where username='$username' and password='$password'",$con);
    $row = mysql_fetch_array($result);
    mysql_close($con);
    if(!$row['id']) return false;
    else return $row['id'];
}
$username=mysql_real_escape_string($_POST['username']);
$password=mysql_real_escape_string($_POST['password']);
$status=mysql_real_escape_string(filter_input(INPUT_POST,'status'));
if($status=='admin'){
    if($_SESSION['id']=AdminValidate($username,$password)){
        $_SESSION['status']='admin';
        header('location: admin/admin.php');
    }
    else{
        header('location: course.php');
        session_destroy();
    }
}
elseif($status=='student'){
    if($_SESSION['id']=StudentValidate($username,$password)){
        $_SESSION['status']='student';
        header('location: student/student.php');
    }
    else{
        header('location: course.php');
        session_destroy();
    }
}
else{
    header('location: course.php');
    session_destroy();
}

?>


