<?php
session_start();

if (!isset($_SESSION['expression'])) {
    $_SESSION['expression'] = '';
}

if (isset($_POST['input']) && $_SESSION['expression'] !== 'Syntacs Error') {
    // Append the input only if the last character is not a zero or if the last character is not a digit
    if ($_POST['input'] !== '0' || !preg_match('/\d$/', $_SESSION['expression'])) {
        $_SESSION['expression'] .= $_POST['input'];
    }
} 

if (isset($_POST['operator']) && $_SESSION['expression'] !== 'Syntacs Error') {
    // Remove any trailing spaces before adding the operator
    $_SESSION['expression'] = rtrim($_SESSION['expression']);

    // Check if the last character is an operator
    $lastChar = substr($_SESSION['expression'], -1);
    if (!in_array($lastChar, ['+', '-', '*', '/'])) {
        $_SESSION['expression'] .= ' ' . $_POST['operator'] . ' ';
    }
} 

if (isset($_POST['dot']) && $_SESSION['expression'] !== 'Syntacs Error') {
    // Check if the last character is a digit and if there is no decimal point already present
    if (preg_match('/\d$/', $_SESSION['expression']) && !preg_match('/\.\d*$/', $_SESSION['expression'])) {
        $_SESSION['expression'] .= '.';
    } elseif (empty($_SESSION['expression'])) {
        // If the expression is empty, start with a zero followed by a decimal point
        $_SESSION['expression'] = '0.';
    }
}

if (isset($_POST['clear'])) {
    $_SESSION['expression'] = '';
}

if (isset($_POST['back']) && $_SESSION['expression'] !== 'Syntacs Error') {
    $_SESSION['expression'] = substr($_SESSION['expression'], 0, -1); // Remove the last character
}

if (isset($_POST['equal']) && $_SESSION['expression'] !== 'Syntacs Error') {
    $expression = $_SESSION['expression'];
    if (!preg_match('/^\d+(\.\d+)?\s*([+\-*\/]\s*\d+(\.\d+)?\s*)*$/', $expression)) {
        $_SESSION['expression'] = 'Syntacs Error';
    } else {
        $result = @eval('return ' . $expression . ';');
        if ($result === false) {
            $_SESSION['expression'] = 'Syntacs Error';
        } else {
            $_SESSION['expression'] = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CASIO</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #008ea3;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .calculator {
            background-color: #333;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            padding: 20px;
            width: 300px;
            transform: scale(1.3);
        }
        .calculator h1 {
            text-align: center;
            margin-top: 0;
            color: #fff;
            font-size: 24px;
        }
        .calculator input[type="text"] {
            width: calc(100% - 0px);
            padding: 10px;
            font-size: 24px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: none;
            border-radius: 5px;
            text-align: right;
            background-color: #2d4500;
            color: #fff;
            outline: none;
        }
        .calculator .buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
        }
        .calculator button {
            padding: 15px;
            font-size: 20px;
            border: none;
            border-radius: 5px;
            background-color: #666;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .calculator button:hover {
            background-color: #888;
        }
        .calculator button.clear {
            grid-column: span 2;
            background-color: #d9534f;
        }
        .calculator button.clear:hover {
            background-color: #c9302c;
        }
        .calculator button.equal {
            grid-column: span 2;
            background-color: #5bc0de;
        }
        .calculator button.equal:hover {
            background-color: #46b8da;
        }
        .calculator button.operator {
            background-color: #f0ad4e;
        }
        .calculator button.operator:hover {
            background-color: #ec971f;
        }
    </style>
</head>
<body>
<div class="calculator">
    <h1>CASIO</h1>
    <span style="font-family:'Times New Roman', Times, serif; font-style:italic;font-size:10px; color:white; float: right;">fx-991 CW</span>
    <form action="" method="post">
        <input type="text" id="mainInput" name="expression" value="<?php echo htmlspecialchars(@$_SESSION['expression']) ?>" <?php if($_SESSION['expression'] === 'Syntacs Error') { echo 'style="color: #ff0000;"'; } ?> readonly>
        
        <div class="buttons">
            <button class="clear" type="submit" name="clear">C</button>
            <button type="submit" name="back">&larr;</button>
            <button class="operator" type="submit" name="operator" value="/">/</button>
            <button class="numbtn" type="submit" name="input" value="7">7</button>
            <button class="numbtn" type="submit" name="input" value="8">8</button>
            <button class="numbtn" type="submit" name="input" value="9">9</button>
            <button class="operator" type="submit" name="operator" value="*">*</button>
            <button class="numbtn" type="submit" name="input" value="4">4</button>
            <button class="numbtn" type="submit" name="input" value="5">5</button>
            <button class="numbtn" type="submit" name="input" value="6">6</button>
            <button class="operator" type="submit" name="operator" value="-">-</button>
            <button class="numbtn" type="submit" name="input" value="1">1</button>
            <button class="numbtn" type="submit" name="input" value="2">2</button>
            <button class="numbtn" type="submit" name="input" value="3">3</button>
            <button class="operator" type="submit" name="operator" value="+">+</button>
            <button class="numbtn" type="submit" name="input" value="0">0</button>
            <button class="numbtn" type="submit" name="dot" value=".">.</button>
            <button class="equal" type="submit" name="equal">=</button>
        </div>
    </form>
</div>
<script>
    // Function to adjust text alignment based on content length
    function adjustTextAlignment() {
        var inputField = document.getElementById("mainInput");
        if (inputField.scrollWidth > inputField.clientWidth) {
            inputField.style.textAlign = "left"; // Align text to the left if it overflows
            inputField.scrollLeft = inputField.scrollWidth - inputField.clientWidth; // Scroll to the end
        } else {
            inputField.style.textAlign = "right"; // Align text to the right if it fits
            inputField.scrollLeft = 0; // Reset scroll to the rightmost position
        }
    }

    // Call the function whenever there's a change in the input field
    document.getElementById("mainInput").addEventListener("input", adjustTextAlignment);

    // Call the function initially to set the initial text alignment
    adjustTextAlignment();
</script>
</body>
</html>
