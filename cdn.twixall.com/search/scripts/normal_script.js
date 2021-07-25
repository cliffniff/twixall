var selected = false;
function close_notice(){
    document.getElementById("header").remove();
}

function suggest(val){
    if(val.length == 0){
        document.getElementById("suggestions").innerHTML = '';
    }
    else{
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200){
                var res = this.response;
                if(res.length != 0){
                    document.getElementById("sug-wrapper").style.display = "block";
                    document.getElementById("suggestions").innerHTML = res;
                }
                else{
                    document.getElementById("suggestions").innerHTML = '';
                }
            }
        }
        xhttp.open('GET', '../static.twixall.com/search/apis/suggestions/?query=' + val, true);
        xhttp.send();
    }
}

function change_omni(text){
    document.getElementById("omnibox-input").value = text;
}

function search_query(){
    if(document.getElementById("omnibox-input").value != ''){
        window.location.href="./search?q=" + document.getElementById("omnibox-input").value;
    }
}