<?php
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");
    header("X-Powered-By: Twixall");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Twixall</title>
        <link rel="icon" href="../cdn.twixall.com/images/icons/Twixtall(64x64).ico" type="image/x-icon"/>
        <link rel="stylesheet" href="../cdn.twixall.com/search/stylesheets/normal_style.css"/>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content="Search through the ocean of web pages with the new privacy focused search engine. We ensures our customers privacy on our websites by encrypting your search keys. We use cookies and do not misuse them." />
        <meta name="keywords" content="search engine,privacy search engine, twixall"/>
        <meta name="og:description" content="Search through the ocean of web pages with the new privacy focused search engine. We ensures our customers privacy on our websites by encrypting your search keys. We use cookies and do not misuse them."/>
        <meta name="og:title" content="Twixall"/>
        <script src="../cdn.twixall.com/search/scripts/normal_script.js"></script>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <div id="privacy-notice-span-div">
                    <span id="privacy-notice-span">We use your information to personalize your content.</span>
                </div>
                <div id="privacy-page-btn-div">
                    <button id="privacy-page-btn" onclick="window.location.href = 'user-privacy'">Learn more.</button>
                </div>
                <span id="close-notice-span" onclick="close_notice()">X</span>
            </div>
            <div id="primary-container"> 
                <div id="logo-container-div">
                    <img id="logo-img" src="../cdn.twixall.com/images/logos/name_and_logo(577x245).png"/>
                </div>
                <div id="omnibox-container-main-div">
                    <div id="omnibox-container-div">
                            <input id="omnibox-input" type="text" value = "" placeholder="Type here..." oninput="suggest(this.value)" autocomplete="off"/><button id="omnibox-submit-btn" onclick="search_query()">Search</button>
                    </div>
                </div>
                <div id="sug-wrapper">
                        <div id="suggestions">
                
                        </div>
                </div>
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
            </div>
        </div>
    </body>
</html>