<?php
    if(isset($_GET['url'])){
        $url = $_GET['url'];
        $id = (int)explode('(:::)', $url)[1];
        $url = explode('(:::)', $url)[0];
        $con = mysqli_connect('localhost', 'root', '', 'search');
        $url = mysqli_real_escape_string($con, $url);
        $sql1 = "SELECT views FROM search.index WHERE id=?";
        $stmt1 = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt1, 'i', $id);
        mysqli_stmt_execute($stmt1);
        $res = mysqli_stmt_get_result($stmt1);
        $views = (int)mysqli_fetch_assoc($res)['views'];
        $views += 10;
        $sql2 = "UPDATE search.index SET views=? WHERE id=?";
        $stmt2 = mysqli_prepare($con, $sql2);
        mysqli_stmt_bind_param($stmt2, 'ii' , $views, $id);
        mysqli_stmt_execute($stmt2);
        header('Location: '.$url);
    }
?>