

$('document').ready(function() { 
	
    $("#logout").click(function(){
        sessionStorage.clear();
        window.location.href = "index.html";
    }); 
	
});