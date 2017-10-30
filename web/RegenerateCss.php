<?php

if(isset($_POST['regenerate'])) {

    require "../dependency/html/plugins/lessphp/lessc.inc.php";
    $less = new lessc;
    $css = $less->compileFile("../dependency/html/public/less/dashboard/style.less");
    file_put_contents("../src/presentation/maarchRM/Resources/public/css/style.css", $css);
    
    exit;
}

?>

<html>
    <body>
        <h1 id="title" style="text-align: center; margin-top:15%">
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

        <script>

            var request = new XMLHttpRequest();
            request.open('POST', 'RegenerateCss.php', true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            request.send("regenerate=OK");

            request.onload = function() {
                document.getElementById("title").innerHTML = "CSS regenerated!";
            };

        </script>
    </body>
</html>