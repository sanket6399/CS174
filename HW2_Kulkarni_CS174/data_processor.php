<?php
class NumberProcessor {
    private $numberString;
    const MAX_NUMBER_LENGTH = 400;
    const MAX_SUBSTR_LENGTH = 20;
    const MAX_DIGITS = 5;

    public function __construct($numberString = '') {
        $this->numberString = $numberString;
    }

    private static function largest_product($line) {
        /* function description: this function calculates the maximum product over a single line of 20 numbers */
        $maxProduct = 0;
        $maxDigits = [];
        // split the line into an array of digits
        $digits = str_split($line);
        // iterate over the array of digits, calculating the product of 5 consecutive digits
        $length = count($digits);
        for ($i = 0; $i <= $length - self::MAX_DIGITS; $i++) {
            // calculate the product of 5 consecutive digits
            $product = $digits[$i] * $digits[$i+1] * $digits[$i+2] * $digits[$i+3] * $digits[$i+4];
            // if the product is greater than the current maximum product, update the maximum product and the digits
            if ($product > $maxProduct) {
                $maxProduct = $product;
                $maxDigits = array_slice($digits, $i, self::MAX_DIGITS);
            }
        }
        // return the maximum product and the digits that produced it
        return ['product' => $maxProduct, 'digits' => $maxDigits];
    }

    private static function factorial($number) {
        $factorial = 1;
        // calculate the factorial of a number
        for ($i = 1; $i <= $number; $i++) {
            $factorial *= $i;
        }
        return $factorial;
    }

    public function validate_and_process($filePath = null) {
        $overallMaxProduct = 0;
        $overallSumFactorials = 0;
        $overallMaxDigits = [];
        // if a file path is provided, read the file and set the number string
        if ($filePath !== null) {
            $this->numberString = htmlentities(file_get_contents($filePath));
        }
        $lines = explode("\n", $this->numberString);
        // remove any new line characters and non-digit characters from the number string
        $this->numberString = preg_replace("/[\r\n]+/", '', $this->numberString);
        // remove any non-digit characters from the number string
        $this->numberString = preg_replace('/[^\d]/', '0', $this->numberString);
        if(strlen($this->numberString) != self::MAX_NUMBER_LENGTH) {
            return "Length of the number string should be 400";
        }
        $start = 0;
        // iterate over the number string, calculating the maximum product over 5 consecutive digits
        for($substr = 1; $substr <= self::MAX_SUBSTR_LENGTH; $substr++) {
            $end = $substr * self::MAX_SUBSTR_LENGTH;
            // calculate the maximum product over 5 consecutive digits
            $result = $this->largest_product(substr($this->numberString, $start, $end));
            $start = $end + 1;
            // if the maximum product is greater than the overall maximum product, update the overall maximum product and the digits
            if ($result['product'] > $overallMaxProduct) {
                $overallMaxProduct = $result['product'];
                $overallMaxDigits = $result['digits'];
                $overallSumFactorials = array_sum(array_map([$this, 'factorial'], $overallMaxDigits));
            }
        }
        // if the overall maximum product is greater than 0, return the overall maximum product and the digits
        if ($overallMaxProduct > 0) {
            return "Largest sum: " . implode(' * ', $overallMaxDigits) . " = $overallMaxProduct — Sum of Factorial = " . implode('! + ', $overallMaxDigits) . "! = $overallSumFactorials";
        } else {
            return "No valid lines with numbers found";
        }
    }
    public static function test_largest_product() {
        $testCases = [
            "good" => [
                "input" => "71636269561882670428\n85861560789112949495\n65727333001053367881\n52584907711670556013\n53697817977846174064\n83972241375657056057\n82166370484403199890\n96983520312774506326\n12540698747158523863\n66896648950445244523\n05886116467109405077\n16427171479924442928\n17866458359124566529\n24219022671055626321\n07198403850962455444\n84580156166097919133\n97999193423380308135\n73167176531330624919\n30358907296290491560\n70172427121883998797",
                "expected" => "Largest sum: 9 * 7 * 9 * 9 * 9 = 45927 — Sum of Factorial = 9! + 7! + 9! + 9! + 9! = 1456560"
            ],
            "bad" => [
                "input" => "ThisIsNotANumberStringButLet'sSeeWhatHappens",
                "expected" => "Length of the number string should be 400"
            ],
            "ugly" => [
                "input" => "hgdcghbaeekfjbhdaaka\ncbakkgchbjeedcjebeja\ncfcjhfjgjhegkdcjbcbb\ngjgkjhdcgcjdbhbbkecg\nbhafhcafdkffhbeechgk\ngfegegdcfgkecbfcjjef\ngjfccdcjcbakfegfbbdh\njbjfccdbfhbgecghfbja\nhhhagfjcgchhfgacjhbd\njdbbgcfjddjdebaheecg\ndkbajffdekbhkbffkfhb\nhfddjhcdheejkakfdhkk\ncjkfghbgddkkjbkbhbea\nkffjfgbhhfgdhdchhfge\ncbhdbgeghcggffjjbfjb\ngecbabgahbdbhgejhbhe\ngkjhbdhgffhhjcfjhecf\njbdjdjfkjcdjehcehjhf\ngdjfkfgdhjcjehgfjbaj\njfhbgfgfhbjjjdfbfjhh",
                "expected" => "No valid lines with numbers found"
            ],
        ];

        foreach ($testCases as $key => $testCase) {
            echo "Testing case: $key<br>";
            $processor = new NumberProcessor($testCase['input']);
            $result = $processor->validate_and_process();
            if ($result === $testCase['expected']) {
                echo "Test Passed! <br>Input: {$testCase['input']}<br>Expected:<br>{$testCase['expected']}<br>Got:<br>{$result}<br><br>";
            } else {
                echo "Test Failed! <br>Input: {$testCase['input']}<br>Expected:<br>{$testCase['expected']}<br>Got:<br>{$result}<br><br>";
            }
        }
    }
    public static function test_largest_product_function() {
        $testCases = [
            "Case 1" => [
                "input" => "12345678901234567890",
                "expected" => ['product' => 15120, 'digits' => [5, 6, 7, 8, 9]]
            ],
            "Case 2" => [
                "input" => "98765432109876543210",
                "expected" => ['product' => 15120, 'digits' => [9, 8, 7, 6, 5]]
            ],
            "Case 3" => [
                "input" => "11111222223333444455",
                "expected" => ['product' => 1600, 'digits' => [4, 4, 4, 5, 5]]
            ],
            "Case 4" => [
                "input" => "99900022220000111199",
                "expected" => ['product' => 81, 'digits' => [1, 1, 1, 9, 9]]
            ],
        ];        
    
        foreach ($testCases as $key => $testCase) {
            echo "Testing case: $key<br>";
            $result = NumberProcessor::largest_product($testCase['input']);
            if ($result['product'] === $testCase['expected']['product']) {
                echo "Test Passed! <br>Input {$testCase['input']}<br>Expected:<br>" . print_r($testCase['expected'], true) . "<br>Got:<br>" . print_r($result, true) . "<br><br>";
            } else {
                echo "Test Failed! <br>Input {$testCase['input']}<br>Expected:<br>" . print_r($testCase['expected'], true) . "<br>Got:<br>" . print_r($result, true) . "<br><br>";
            }
        }
    }
    
}

if (isset($_FILES['numberFile'])) {
    // get the file type
    $fileType = $_FILES['numberFile']['type'];
    echo "File type: $fileType<br>";
    // check if the file type is text/plain
    if ($fileType != 'text/plain') {
        echo "Only .txt files are allowed.";
    } else {
        $processor = new NumberProcessor();
        $uploadStatus = htmlentities($processor->validate_and_process($_FILES['numberFile']['tmp_name']));
        echo $uploadStatus;
        
    }
} else {
    echo "No file uploaded.";
}
echo"<br>----------------------------------------<br>";
echo "<br>Testing validation function<br><br>";
NumberProcessor::test_largest_product();
echo"<br>----------------------------------------<br>";
echo "<br>Testing largest product function: utility function<br>";
NumberProcessor::test_largest_product_function();

