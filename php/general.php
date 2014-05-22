<?php

function redirect($url, $method, $paramArray) {
    if (isset($paramArray)) {
        switch ($method) {
            case "get":
                $params = setUpParams($paramArray);
                header("Location: " . $url . $params);
                break;
            case "post":
                echo '<form action="' . $url . '" method="post" id="form">';
                $numberOfParams = sizeOf($paramArray);
                $titles = array_keys($paramArray);
                for ($inc = 0; $inc < $numberOfParams; $inc ++) {
                    echo '<input type="hidden" name="' . $titles[$inc] . '" value="' . $paramArray[$titles[$inc]] . '"/>';
                }
                echo '</form>';
                echo '<script type="text/javascript"> document.getElementById("form").submit()</script>';
                break;
        }
    } else {
        header("Location: " . $url);
    }
}

function setUpParams($paramArray) {
    $titles = array_keys($paramArray);
    $data = $titles[0] . "=" . $paramArray[$titles[0]];
    for ($inc = 1; $inc < count($paramArray); $inc ++) {
        $current = $titles[$inc];
        if (!empty($paramArray[$current])) {
            $data .= "&" . $current . "=" . urlencode($paramArray[$current]);
        }
    }
    return $data;
}

function displaySessionValue($name, $default) {
    $val = "";
    if (isset($_SESSION[$name])) {
        $val = $_SESSION[$name];
    } else {
        $val = $default;
    }
    return $val;
}

function loadSession ($array) {
     echo "<script type=\"text/javascript\">
         function getValue(name) {
            var value = \"-1\";
            //document.write(name);
            switch(name) {
            ";
     $titles = array_keys($array);
     for ($inc = 0; $inc < count($array); $inc ++) {
         $current = $array[$titles[$inc]];
         echo "case \"" . $titles[$inc] . "\":
                        value = urldecode(\"" . $current . "\");
                        break;";
         
     }
     echo "}
         return value;
         }
     </script>";
}

?>
