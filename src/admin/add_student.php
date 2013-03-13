<?php session_start();
if(empty($_SESSION['status']) || $_SESSION['status']!='admin'){
    session_destroy();
}
else{
    if($_POST['username'] && $_POST['password'] && $_POST['name']){
        $username=mysql_real_escape_string($_POST['username']);
        $password=mysql_real_escape_string($_POST['password']);
        $name=mysql_real_escape_string($_POST['name']);
        $con=mysql_connect('localhost','css','public');
        mysql_select_db('course_selection_system',$con);
        $result = mysql_query('insert into student (username,password,name,courses) values ("'.$username.'","'.$password.'","'.$name.'","")');
        mysql_close($con);
    }
}
?>
