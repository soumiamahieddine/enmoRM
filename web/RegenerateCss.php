<?php

require "../dependency/html/plugins/lessphp/lessc.inc.php";


$less = new lessc;
$css = $less->compileFile("../dependency/html/public/less/dashboard/style.less");
file_put_contents("presentation/maarchRM/Resources/public/css/style.css", $css);

echo "css regenerated !";
