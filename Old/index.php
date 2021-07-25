<?php
	$file = fopen('visits.txt', 'a');
	date_default_timezone_set('Asia/Colombo');
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$time = date("h:i:sa");
	$date = date("Y-m-d-l");
	fwrite($file, "IP:".$_SERVER['REMOTE_ADDR'].","."User Agent:".$user_agent.","."Time:".$time.","."Date:".$date."\n");
	fclose($file);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'/>
        <title>Twixall | Web Search</title>
        <meta name='description' content='Search the web with Twixall, and help us to improve'/>
        <meta name='keywords' content='Search,Engine,Web,Twixall'/>
		<meta name="theme-color" content="#0000FF">
        <link rel='stylesheet' href='style.css'/>
        <link rel='shortcut icon' href='images/favicon.ico'/>
        <script src='script.js'></script>
    </head>
    <body>
        <center>
            <div id="space">
                
            </div>
            <div id='container'>
                <div id='logo-container'>
                    <img id='logo' alt='Twixall Search' src='images/name_and_logo(577x245).png'>
                </div>
                <div id='search-container'>
                    <form method='GET' action='search/'>
                        <input id='search' type='search' placeholder='Type to search anything' autocomplete="off" name='q' title='Type to Search' autofocus><input type='submit' id='submit' value="Search" title='Type to search'>
                    </form>
					<br>
                </div>
            </div>
        </center>
        <div id='footer'>
			<h6 style="text-align:center;">Copyright Twixall-2019. All Rights Reserved.</h6>
        </div>
    </body>
</html>