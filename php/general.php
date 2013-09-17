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
    echo "<script> var inp = document.getElementsByName(" . $name . ");";
    echo "inp.setAttribute(\"name\",\"" . $val . "\");</script>" ;
    
}

function sortArray($array) {
    $arrLen = sizeOf($array);
    if ($arrLen == 2) {
        if ($array[0] <= $array[1]) {
            return $array;
        } else {
            $temp = $array[0];
            $array[0] = $array[1];
            $array[1] = $temp;
            return $array;
        }
    } else {
        $split = array_chunk($array, ($arrLen / 2));
        return mergeArray(sortArray($split[0]), sortArray($split[1]));
    }
}

function mergeArray($array1, $array2) {
    if (isset($array1)) {
        $titles1 = array_keys($array1);
        $titles2 = array_keys($array2);
        if ($array1[$titles1[0]] > $array2[$titles[0]]) {
            $newArray2 = array($titles1[0] => $array1[$titles1[0]]);
            $newArray2 = array_merge($newArray2, $array2);
            mergeArray();
        } else {
            
        }
    }
}

?>
