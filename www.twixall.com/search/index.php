<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("X-Powered-By: Twixall");
$con = mysqli_connect('localhost', 'root', '', 'search');
mysqli_set_charset($con, 'utf8');
$ip = '123.231.108.156';
$json_details = file_get_contents("https://api.ipdata.co/".$ip."/?api-key=ea3cd6157149ea05dda39c99eaecb298a9a91698a09c0aa8256cef1b");
$json_decoded = json_decode($json_details, true);
$longitude = $json_decoded['longitude'];
$latitude = $json_decoded['latitude'];
$city = $json_decoded['city'];
$region = $json_decoded['region'];
$country = $json_decoded['country_name'];
$continent = $json_decoded['continent_name'];
$org = $json_decoded['organisation'];
$flag = $json_decoded['emoji_flag'];
$time = $json_decoded['time_zone']['current_time'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$ls = 'Long:'.$longitude.';Lat:'.$latitude;
$loc = 'City:'.$city.';Region:'.$region.';Country:'.$country.';Continent:'.$continent.';Organisation:'.$org.';Flag:'.$flag.';Time:'.$time.';UserAgent:'.$user_agent;
if(isset($_GET['q'])){
    $q = trim($_GET['q']);
    $dat = '[Ip:'.$ip.';'.$ls.';'.$loc.";".$q."]\n";
    file_put_contents("../../static.twixall.com/search/ips.txt", $dat, FILE_APPEND);
    if(strlen($q) == 0){
        header("Location: http://localhost/Search/www.twixall.com/", true, 200);
    }
    $q = mysqli_real_escape_string($con, $q);
    $q = addcslashes($q, '%_');
    $sug_sql = "INSERT INTO suggestions(query) VALUES(?)";
    $sug_p = mysqli_prepare($con, $sug_sql);
    mysqli_stmt_bind_param($sug_p, 's', $q);
    mysqli_stmt_execute($sug_p);
    if(isset($_GET['page'])){
        $page = $_GET['page'];
    }
    else{
        $page = 1;
    }
    $query_words = explode(' ',$q);
    $syns = file('../../static.twixall.com/search/syns.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
    $matched = [];
    foreach($syns as $syn){
        $s = explode(':', $syn);
        foreach($s as $sy){
            if(in_array($sy, $query_words)){
                unset($s[$sy]);
                $matched[$sy] = $s;
            }
        }
    }
    $build = "SELECT * FROM search.index WHERE";
    $words = explode(' ',$q);
    $c = 1;
    foreach($words as $word){
        if($c == count($words)){
            $build .= " (title LIKE '%$word%' OR description LIKE '%$word%' OR texts LIKE '%$word%')";
        }
        else{
            $build .= " (title LIKE '%$word%' OR description LIKE '%$word%' OR texts LIKE '%$word%') AND";
        }
        $c++;
    }
    $query_2 = '';
    foreach($matched as $key => $values){
        $query_2 = implode(' ', $values);
    }
    $c = 1;
    foreach($words as $word){
        if($c == count($words)){
            $build .= " AND (title LIKE '%$word%' OR description LIKE '%$word%' OR texts LIKE '%$word%') LIMIT ".(string)((int)$page * 10);
        }
        else{
            $build .= " AND (title LIKE '%$word%' OR description LIKE '%$word%' OR texts LIKE '%$word%')";
        }
        $c++;
    }
    $stmt = mysqli_prepare($con, $build);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $api_url = "https://en.wikipedia.org/w/api.php?action=opensearch&search=".$q."&limit=1&namespace=0&format=json";
    $assoc = json_decode(file_get_contents($api_url), true);
    if(!empty($assoc[1])){
        $title = $assoc[1][0];
        $des = $assoc[2][0];
        $url = $assoc[3][0];
        $wiki = true;
    }
}
else{
    $dat = '[Ip:'.$ip.';'.$ls.';'.$loc.";No query"."]\n";
    file_put_contents("../../static.twixall.com/search/ips.txt", $dat, FILE_APPEND);
    header("Location: http://localhost/Search/www.twixall.com/", true, 200);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $q.' - Twixall'; ?></title>
        <link rel="icon" href="../../cdn.twixall.com/images/icons/Twixtall(64x64).ico" type="image/x-icon"/>
        <link rel="stylesheet" href="../../cdn.twixall.com/search/stylesheets/search.css" type="text/css">
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content="Search through the ocean of web pages with the new privacy focused search engine. We ensures our customers privacy on our websites by encrypting your search keys. We use cookies and do not misuse them." />
        <meta name="keywords" content="search engine,privacy search engine, twixall"/>
        <meta name="og:description" content="Search through the ocean of web pages with the new privacy focused search engine. We ensures our customers privacy on our websites by encrypting your search keys. We use cookies and do not misuse them."/>
        <meta name="og:title" content="Twixall"/>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <div id="logo-container-div">
                    <img id="logo-img" src="../../cdn.twixall.com/images/logos/std_search_logo[288 x 263].png"/>
                </div>
                <div id="omnibox-container-main-div">
                    <div id="omnibox-container-div">
                            <input id="omnibox-input" type="text" value = <?php echo $q;?> placeholder="Type here..." oninput="suggest(this.value)" autocomplete="off"/><button id="omnibox-submit-btn" onclick="search_query()">Search</button>
                    </div>
                </div>
                <div id="sug-wrapper">
                        <div id="suggestions">
                
                        </div>
                </div>
            </div>
            <div id="primary-container">
                <div id="blog-result-container">
                    <?php
                        if(isset($wiki)){
                            echo '<h2>'.$title.'</h2><br>';
                            echo '<p>'.$des.'</p><br>';
                            echo '<a href="'.$url.'">&#x1f6c8; Wikipedia</a>';
                        }
                    ?>
                </div>
                <div id="results-container-div">
                    <?php
                        if(mysqli_num_rows($res) > 0){
                            $r_store = array();
                            while($row = mysqli_fetch_assoc($res)){
                                $r_store[(int)$row['id']] = $row;
                            }        
                            $p_and_id = array();
                            $tlds = array('com','org','net','gov','biz','edu');
                            foreach($r_store as $id => $row){
                                $points = 0;
                                $points += substr_count(strtolower($row['texts']), strtolower($q));
                                if(strtolower(substr($row['title'], 0, strlen($q))) == strtolower($q)){
                                    $points++;
                                }
                                $parsed_url = parse_url($row['url']);
                                $domains = explode('.', $parsed_url['host']);
                                if(count($domains) == 2){
                                    $tld = $domains[1];
                                    $ho = $domains[0];
                                }
                                else{
                                    $tld = $domains[2];
                                    $ho = $domains[1];
                                    $subd = $domains[0];
                                }
                                if(in_array($tld, $tlds)){
                                    $points++;
                                }
                                if($ho == strtolower($q)){
                                    $points += 40;
                                }
                                if(strtolower($row['title']) == strtolower($q)){
                                    $points += 10;
                                }
                                if(isset($subd)){
                                    if($q == $subd){
                                        $points += 10;
                                    }
                                    else if(substr($subd, 0, strlen($q)) == $q){
                                        $points += 5;
                                    }
                                }
                                $points += $row['views'];
                                $p_and_id[$id] = $points;
                            }
                            arsort($p_and_id);
                            foreach($p_and_id as $id=>$points){
                                $data = $r_store[$id];
                                $title = (strlen($data['title']) > 50) ? substr($data['title'], 0, 47).'...' : $data['title'];
                                $description = $data['description'];
                                $description = (strlen($description) > 150) ? substr($description, 0, 147).'...' : $description;
                                $url = (strlen($data['url']) > 30) ? substr($data['url'], 0, 27).'...' : $data['url'];
                                $ad = false;
                                if($data['ad'] == 1){
                                    $ad = true;
                                }
                                if($ad){
                                    echo '<div id="result" title="'.$points.'"><a href="'.'http://localhost/Search/static.twixall.com/redirect_search/?url='.$data['url']."(:::)".$data['id'].'"><h4 id="result-title">'.$title.'</h4></a><br><span style="padding:2px;border:1px solid green;color:green;margin-right:3px;border-radius:6px;font-family:\'medium\'">Ad</span><span id="result-url">&#5125;'.$url.'</span><br><p id="result-description">'.$description.'</p></div><br>';
                                }else{
                                    echo '<div id="result" title="'.$points.'"><a href="'.'http://localhost/Search/static.twixall.com/redirect_search/?url='.$data['url']."(:::)".$data['id'].'"><h4 id="result-title">'.$title.'</h4></a><br><span id="result-url">&#5125; '.$url.'</span><br><p id="result-description">'.$description.'</p></div><br>';
                                }
                            }
                        }
                    ?>
                </div>
            </div>

        </div>
        <script src="../../cdn.twixall.com/search/scripts/search.js"></script>
        <script>
            document.getElementById("omnibox-input").addEventListener('blur', function(){
                if(!selected){
                    document.getElementById("sug-wrapper").style.display = 'none';
                }
            });document.getElementById("sug-wrapper").addEventListener('mouseleave', function(){
                if(document.activeElement != document.getElementById("omnibox-input")){
                    document.getElementById("sug-wrapper").style.display = 'none';
                }
            });
            document.getElementById("omnibox-input").addEventListener("keydown", function(event){
                key = event.which || event.keyCode;
                if(key == 13 && document.getElementById("omnibox-input").value.trim() != ''){
                    search_query();
                }
            });
        </script>
    </body>
</html>