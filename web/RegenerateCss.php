<?php

if(isset($_POST['regenerate'])) {
    $fileName = $_POST['file'];
    require "../dependency/html/plugins/lessphp/lessc.inc.php";
    $less = new lessc;
    $css = $less->compileFile("public/less/dashboard/style.less");
    file_put_contents("public/css/$fileName", $css);
    
    exit;
}

?>

<html>
    <body>
    <div id="filename_form" style="text-align: center; margin-top:15%">
        <h1>File name</h1>
        <input id="filename" type="text">
        <button onclick="generate()">Send</button>
        <span style="color: red; display: none"> You can't create style.css</span>
    </div>
        <h1 id="title" style="text-align: center; margin-top:15%;display: none">
            <span>Css regeneration processing...</span>
            <br/>
            <svg width="78px"  height="78px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-dual-ring" style="background: none;">
                <circle cx="50" cy="50" ng-attr-r="{{config.radius}}" ng-attr-stroke-width="{{config.width}}" ng-attr-stroke="{{config.c1}}" ng-attr-stroke-dasharray="{{config.dasharray}}" fill="none" stroke-linecap="round" r="40" stroke-width="5" stroke="#5fa3d1" stroke-dasharray="62.83185307179586 62.83185307179586" transform="rotate(204 50 50)">
                    <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
                </circle>
                <circle cx="50" cy="50" ng-attr-r="{{config.radius2}}" ng-attr-stroke-width="{{config.width}}" ng-attr-stroke="{{config.c2}}" ng-attr-stroke-dasharray="{{config.dasharray2}}" ng-attr-stroke-dashoffset="{{config.dashoffset2}}" fill="none" stroke-linecap="round" r="34" stroke-width="5" stroke="#eab262" stroke-dasharray="53.40707511102649 53.40707511102649" stroke-dashoffset="53.40707511102649" transform="rotate(-204 50 50)">
                    <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;-360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
                </circle>
            </svg>
        </h1>
        <p id="description" style="text-align: center"></p>
        <script>
            function generate() {

                if(document.getElementById("filename").value){
                    var file =  document.getElementById('filename').value;

                    if(file != "style.css") {

                        document.getElementById('title').style.display='block';
                        document.getElementById('filename_form').style.display='none';

                        var request = new XMLHttpRequest();
                        request.open('POST', 'RegenerateCss.php', true);
                        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                        request.send("regenerate=OK&file="+file);

                        request.onload = function() {
                            document.getElementById("title").innerHTML = "CSS regenerated!";
                            document.getElementById("description").innerHTML = "To activate the new CSS, you have to replace the CSS configuration with the generated file location in the [presentation.maarchRM] section.";
                        };
                    } else {
                        document.getElementById('filename').style.borderColor='#e52213';
                        document.getElementById('filename_form').getElementsByTagName('span')[0].style.display='block'
                    }
                }
            }

        </script>
    </body>
</html>