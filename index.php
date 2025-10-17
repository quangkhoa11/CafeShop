<?php
    error_reporting(1);
    session_start();
    require("class/classdb.php");
    require("assets/header.php");
    if(isset($_GET['page'])){
        $page=$_GET['page'];
    }else{
        $page='home';
    }
    if ($_GET['page'] == 'verify_otp') {
    include 'page/register/verify_otp.php';
}
    if(isset($_GET['cate'])){
        $cate=$_GET['cate'];
    }
    if(file_exists("page/".$page."/index.php")){
        include("page/".$page."/index.php");
    }
    include("assets/footer.php");

?>
