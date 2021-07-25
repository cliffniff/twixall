<?php
    if(isset($_GET['query'])){
        $q = strtolower($_GET['query']);
        if($q != '' or $q != null){
            $con = mysqli_connect('localhost', 'root', '', 'search');
            $q = mysqli_real_escape_string($con, $q);
            $q = addcslashes($q, '%_');
            $sql = "SELECT DISTINCT `query` FROM `suggestions` WHERE `query` LIKE ? LIMIT 6";
            $stmt = mysqli_prepare($con, $sql);
            $param = $q.'%';
            mysqli_stmt_bind_param($stmt, 's', $param);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($res) > 0){
                $matched = [];
                while($row = mysqli_fetch_assoc($res)){
                    $query = strtolower($row['query']);
                    array_push($matched, $query);
                }
                $matched = array_unique($matched);
                foreach($matched as $match){
                    $mes = "<span class='suggestions-a' onmouseleave='selected=false;' onmouseenter='selected=true;' onclick='change_omni(\"".$match."\")'>".$match."</span><br/>";
                    echo $mes;
                }
            }
            mysqli_close($con);
        }
    }
?>