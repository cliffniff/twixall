<?php
if (isset($_REQUEST['q'])){
	$q = $_REQUEST['q'];
}
else{
	echo '<script>window.location.href="../"</script>';
}
if (isset($_REQUEST['p'])){
	$page = $_REQUEST['p'];
}
else{
	$page = 1;
}
if ($q == '' or $q == ' ') {
    echo '<script>window.location.href="../"</script>';
}
if ($q == '.') {
    $q = 'fullstop punctuation';
}
if ($q == '?') {
    $q = 'question mark';
}
$con = mysqli_connect('localhost', 'root', '', 'web');
mysqli_real_escape_string($con, $q);
$my_sql = 'INSERT INTO `terms`(`term`) VALUES ("'.$q.'")';
$res = mysqli_query($con, $my_sql);
mysqli_set_charset($con, 'utf8');
$data = file('no_of_searches.txt');
$newdata = (int)$data[0] + 1;
$write = fopen('no_of_searches.txt' , 'w');
fwrite($write, "$newdata");
fclose($write);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Twixall Search - <?php echo $q; ?></title>
        <meta charset='UTF-8'/>
		<meta name="theme-color" content="#0000FF">
        <link rel='shortcut icon' href='../images/favicon.ico'/>
        <link rel='stylesheet' href='style.css'/>
    </head>
    <body>
        <div id='container'>
            <img id='logo' alt='Twixall Search' src='../images/name_and_logo(577x245).png'>
            <form method='GET' action='' id='form'>
                <input id='search' type='search' placeholder='Type to search anything' autocomplete="off" name='q' autofocus <?php echo "value = '" . $q . "'"; ?>>
                <input type='submit' value='Search' id='submit'>
            </form>
        </div>
        <br>
        <div id='results'>
            <?php
            $terms = explode(" ", $q);
            $counter = 0;
            $build = "";
            foreach ($terms as $term) {
                $counter++;
                if ($counter == 1) {
                    $build .= "`title` LIKE '$term%' OR `description` LIKE '$term%' OR `url` LIKE '$term%' OR `html` LIKE 'term%' ";
                } else {
                    $build .= "AND `title` LIKE '$term%' OR `description` LIKE '$term%' OR `url` LIKE '$term%' OR `html` LIKE 'term%' ";
                }
            }
            $sql = 'SELECT * FROM `index` WHERE' . $build . 'ORDER BY `points`';
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) > 0) {
                echo '<h6 id="no-res">No. of Results : ' . mysqli_num_rows($result) . '<br></h6>';
                $details = array();
                $points = array();
                $ranked = array();
                $c = 0;
                while($row = mysqli_fetch_assoc($result)){
					$mypoints = $row['points'];
					$details[$row['id']]['url'] = $row['url'];
					$details[$row['id']]['title'] = $row['title'];
					$details[$row['id']]['description'] = $row['description'];
					$details[$row['id']]['html'] = $row['html'];
					$details[$row['id']]['image'] = $row['image'];
					foreach($terms as $t){
						$mypoints += substr_count($row['description'], $t) + substr_count($row['title'], $t) +substr_count($row['url'], $t)+ substr_count($row['html'], $t);
						if(strtolower($row['title']) == strtolower($q)){
							$mypoints +=10000;
						}
						if($row['description'] == 'No Information Available'){
							$mypoints -= 100;
							$details[$row['id']]['description'] = $row['paragraph'];
						}
					}
					$points[$row['id']] = $mypoints;
                }
				arsort($points);
                foreach($points as $key => $val){
                    $ranked[$key]['url'] = $details[$key]['url'];
                    $ranked[$key]['title'] = $details[$key]['title'];
                    $ranked[$key]['description'] = $details[$key]['description'];
					$ranked[$key]['html'] = $details[$key]['html'];
					$ranked[$key]['image'] = $details[$key]['image'];
                }
				$x = 0;
                foreach($ranked as $r => $v){
					if ($ranked[$r]['title'] == 'No Title'){
						continue;
					}
					if ($x ==  0 && strtolower($row['title']) == strtolower($q)){
						echo '<div class = "big_result"><center><h4><a href = "'.$ranked[$r]['url'].'"target = "_blank" title = "'.$ranked[$r]['url'].'">'.$ranked[$r]['title'].'</a></h4>';
						echo '<p>'.$ranked[$r]['description'].'</p>';
						echo '<cite>'.$ranked[$r]['url'].'</cite><br><br></center></div>';
					}
					else{
						echo '<div class = "result"><h4><a href = "'.$ranked[$r]['url'].'"target = "_blank" title = "'.$ranked[$r]['url'].'">'.$ranked[$r]['title'].'</a></h4>';
						echo '<p>'.$ranked[$r]['description'].'</p>';
						echo '<span>'.$ranked[$r]['url'].'</span><br></div>';
					}
					$x += 1;
                }
            } else {
                echo '<marquee><div style="border:1px dashed black;display:table-cell;"><div><h1>No results</h1>';
                echo '<hr>';
                echo '<i><b>"Sorry!We didn`t found any matches for your terms in our databases.<br>We will update our results soon.Please try another term."</b></i><br>';
                echo '<hr></div></div></marquee>';
            }
            mysqli_close($con);
            ?>
        </div>
    </body>
</html>