<?php

class LRUCache {
    private $cacheSize;
    private $cache;
    private $count;
    private $LRU_CACHE_SIZE;

    public function __construct($cacheSize) {
        $this->cacheSize = $cacheSize;
        $this->cache = [];
        $this->count = 0;
        $this->LRU_CACHE_SIZE = $cacheSize;
    }

    private function is_full() {
        return $this->count >= $this->LRU_CACHE_SIZE;
    }

    public function get($key) {
        if (array_key_exists($key, $this->cache)) {
            $value = $this->cache[$key];
            unset($this->cache[$key]);
            $this->cache[$key] = $value;
            return $value;
        } else {
            return -1;
        }
    }

    private function reset() {
        $this->cache = [];
        $this->count = 0;
    }
    public function put($key, $value, $reset) {
        if ($reset)
            $this->reset();

        if ((int)$value < 0 || (int)$key < 0)
            return "Put Cannot be executed. Negative key or values not accepted.";
        
        if(preg_match('/[^0-9]/', $key) > 0)
            return "Put Cannot be executed. Key should be an integer";

        if(preg_match('/[^0-9]/', $value) > 0)
            return "Put Cannot be executed. Value should be an integer";

        $value = (int)$value;
        $key = (int)$key;

        if ($this->is_full() && !array_key_exists($key, $this->cache)) 
            $this->cache = array_slice($this->cache, 1, null, true);
        else
            $this->count += 1;


        unset($this->cache[$key]);
        $this->cache[$key] = $value;

        return "Put executed successfully";
    }

    public function processCommandsFromFile($filePath) {
        echo '<span style="font-size: larger;"><strong>Output:</strong></span><br>';
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = htmlentities($line); // Sanitize input
            $parts = explode(' ', $line);
            if (count($parts) === 1) {
                echo "<br>Get($parts[0]) => " . $this->get($parts[0]) . " || Cache is: " .json_encode($this->cache)."<br>";
            } elseif (count($parts) === 3) {
                $result = $this->put($parts[0], (int)$parts[1], filter_var($parts[2], FILTER_VALIDATE_BOOLEAN));
                if($result == "Put Cannot be executed. Negative key or values not accepted." || $result == "Put Cannot be executed. Key should be an integer" || $result == "Put Cannot be executed. Value should be an integer"){
                    echo "<br>Put($parts[0], $parts[1], $parts[2]) " . "|| Cache is: " .json_encode($this->cache). "<br>".$result . "<br>";
                }else{
                    echo "<br>Put($parts[0], $parts[1], $parts[2]) " . "|| Cache is: " . json_encode($this->cache) . "<br>";
                }
            } else {
                echo "Invalid command in file: $line<br>";
            }
        }
    }

    public static function tester() {
        echo "<h2>LRU Cache Test Cases</h2>";
        // Initialize the cache for testing
        $cache = new LRUCache(LRU_CACHE_SIZE);
    
        // Good case: Insert and retrieve values correctly
        echo "<h3>Good Case</h3>";
        $cache->put(1, 100, false);
        $cache->put(2, 200, false);
        $result1 = $cache->get(1);
        if ($result1 === 100) {
            echo "Good Case: put(1, 100, false), for validation calling get after put<br>";
            echo "Expected: 100<br>";
            echo "Oberserved: $result1<br>";
            echo "Good Case Passed: put(1, 100, false) and get(1) returned 100 as expected.<br>";
        } else {
            echo "Good Case Failed: Expected 100, got $result1.<br>";
        }
    
        // Bad case: Attempt to insert negative or float values
        echo "<h3>Bad Case</h3>";
        $response1 = $cache->put(3, -300, false);
        if ($response1 === "Put Cannot be executed. Negative key or values not accepted.") {
            echo "Bad Case: put(3, -300, false) for validation calling put with negative value<br>";
            echo "Expected: Put Cannot be executed. Negative key or values not accepted.<br>";
            echo "Oberserved: $response1<br>";
            echo "Bad Case Passed: put(3, -300, false) correctly refused negative value.<br>";
        } else {
            echo "Bad Case Failed: Negative value was not handled correctly.<br>";
        }
        $response2 = $cache->put(4, 4.5, false); // Assuming your put method is adjusted to catch floats
        if ($response2 === "Put Cannot be executed. Value should be an integer") {
            echo "<br>Bad Case: put(4, 4.5, false) for validation calling put with float value<br>";
            echo "Expected: Put Cannot be executed. Value should be an integer.<br>";
            echo "Oberserved: $response2<br>";
            echo "Bad Case Passed: put(4, 4.5, false) correctly refused float value.<br>";
        } else {
            echo "Bad Case Failed: Float value was not handled correctly.<br>";
        }
    
        // Ugly case: Attempt to insert a string value
        echo "<h3>Ugly Case</h3>";
        $response3 = $cache->put("keyString", 500, false); // Assuming your put method is adjusted to catch string keys
        if ($response3 === "Put Cannot be executed. Key should be an integer") {
            echo "Ugly Case: put('keyString', 500, false) for validation calling put with string key<br>";
            echo "Expected: Put Cannot be executed. Key should be an integer.<br>";
            echo "Oberserved: $response3<br>";
            echo "Ugly Case Passed: put('keyString', 500, false) correctly refused string key.<br>";
        } else {
            echo "Ugly Case Failed: String key was not handled correctly.<br>";
        }
        $response4 = $cache->put(5, "stringValue", false); // Assuming your put method is adjusted to catch string values
        if ($response4 === "Put Cannot be executed. Value should be an integer") {
            echo "<br>Ugly Case: put(5, 'stringValue', false) for validation calling put with string value<br>";
            echo "Expected: Put Cannot be executed. Value should be an integer.<br>";
            echo "Oberserved: $response4<br>";
            echo "Ugly Case Passed: put(5, 'stringValue', false) correctly refused string value.<br>";
        } else {
            echo "Ugly Case Failed: String value was not handled correctly.<br>";
        }
    }
    
}


define("LRU_CACHE_SIZE", 3);
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["uploadedFile"]) && $_FILES["uploadedFile"]["error"] == 0) {
        $filename = htmlentities($_FILES['uploadedFile']['tmp_name']);
        $fileType = htmlentities($_FILES['uploadedFile']['type']);
        if ($fileType != 'text/plain') {
            echo "Only .txt files are allowed";
        }else{
            $lruCache = new LRUCache(LRU_CACHE_SIZE);
            $lruCache->processCommandsFromFile($filename);
            lrucache::tester();
            echo '<span style="font-size: larger;"><br><strong>Want to try again?</strong></span><br>';
        }
        
    } else {
        echo "Error in file upload.<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LRU Cache File Upload</title>
</head>
<body>
    <h2>Upload a File for LRU Cache Processing</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="fileUpload">Select file to upload:</label>
        <input type="file" name="uploadedFile" id="fileUpload">
        <input type="submit" value="Upload File" name="submit">
    </form>
</body>
</html>
