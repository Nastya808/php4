<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Интернет-голосование</title>
    <style>
        .vote-button {
            background-color: limegreen;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            color: white;
        }
        .results-table {
            margin-top: 20px;
            width: 50%;
            border-collapse: collapse;
        }
        .results-table th, .results-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .bar-container {
            width: 100%;
            background-color: #f1f1f1;
            height: 20px;
            position: relative;
        }
        .bar {
            height: 100%;
            background-color: lightblue;
        }
    </style>
</head>
<body>
<h1>Интернет-голосование</h1>
<p>Какому языку программирования Вы отдали бы предпочтение?</p>

<form method="POST" action="vote.php">
    <label><input type="radio" name="language" value="C++" required>C++<br>
    <input type="radio" name="language" value="C#"> C#<br>
    <input type="radio" name="language" value="JavaScript"> JavaScript<br>
    <input type="radio" name="language" value="PHP"> PHP<br>
    <input type="radio" name="language" value="Java"> Java<br><br>
    <button type="submit" class="vote-button">Голосовать</button>
    </label>
</form>

<?php
$file_path = 'votes.json';
$ip_address = $_SERVER['REMOTE_ADDR'];
$current_time = time();
$wait_time = 3600;

if (file_exists($file_path)) {
    $data = json_decode(file_get_contents($file_path), true);
} else {
    $data = [
        'votes' => [
            'C++' => 0,
            'C#' => 0,
            'JavaScript' => 0,
            'PHP' => 0,
            'Java' => 0
        ],
        'ip_addresses' => []
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
    $language = $_POST['language'];

    if (isset($data['ip_addresses'][$ip_address]) && ($current_time - $data['ip_addresses'][$ip_address] < $wait_time)) {
        echo "<p>Вы уже голосовали недавно. Пожалуйста, подождите, прежде чем голосовать снова.</p>";
    } else {
        if (isset($data['votes'][$language])) {
            $data['votes'][$language]++;
            $data['ip_addresses'][$ip_address] = $current_time;
            file_put_contents($file_path, json_encode($data));
            echo "<p>Спасибо за ваш голос!</p>";
        }
    }
}

$total_votes = array_sum($data['votes']);
$max_votes = max($data['votes']);
if ($total_votes > 0) {
    echo "<h2>Результаты голосования</h2>";
    echo "<table class='results-table'>";
    echo "<tr><th>Язык программирования</th><th>Процент голосов</th><th>График</th></tr>";

    foreach ($data['votes'] as $lang => $count) {
        $percentage = ($count / $total_votes) * 100;
        $bar_width = ($count / $max_votes) * 100;
        echo "<tr>";
        echo "<td>$lang</td>";
        echo "<td>" . round($percentage, 2) . "%</td>";
        echo "<td><div class='bar-container'><div class='bar' style='width: " . $bar_width . "%'></div></div></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Голосов пока нет.</p>";
}
?>
</body>
</html>
