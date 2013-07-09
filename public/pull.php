<?php

if (!isset($_GET['room'])) {
    die('No room ID requested.');
}

if (!isset($_GET['last'])) {
    die('No row specified.');
}

$roomId = $_GET['room'];
$last = $_GET['last'];

if (!preg_match('/^[0-9]+$/', $roomId)) {
    die('Room ID invalid.');
}

if (!preg_match('/^[0-9]+$/', $last)) {
    die('Row ID invalid.');
}

/* Disabled datetime version because it ignored seconds in the SQL.
if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $last)) {
    // If no date is requested start from the time of the request
    $last = date('Y-m-d H:i:s');
}
*/

$watchSettings = [
    'increment' => 1,
    'timeout' => 35,
];

$zendFramework2Config = include '../config/autoload/local.php';

$dbSettings = $zendFramework2Config['doctrine']['connection']['orm_default']['params'];

function getNewRows($dbSettings, $roomId, $last) {
    /**
     * @var $host
     * @var $port
     * @var $user
     * @var $password
     * @var $dbname
     */
    extract($dbSettings);

    $connection = mysqli_connect($host, $user, $password, $dbname, $port);

    if (mysqli_connect_errno()) {
        die('Failed to connect.');
    }

    $query = <<<SQL
        SELECT
          row.id as row_id,
          row.time as row_time,
          user.user_id as user_id,
          user.display_name as user_displayName,
          row_type.name as type_name,
          message.value as message_value,
          GROUP_CONCAT(roll.value ORDER BY roll.id ASC) as roll_values,
          roll.sides as roll_sides
        FROM row
          LEFT JOIN user ON row.user_id = user.user_id
          LEFT JOIN row_type ON row.type_id = row_type.id
          LEFT JOIN message ON row.id = message.row_id
          LEFT JOIN roll ON row.id = roll.row_id
        WHERE row.room_id = $roomId
          AND row.id > $last
        GROUP BY row.id
        ORDER BY row.time ASC
        LIMIT 0, 20
SQL;

    $result = mysqli_query($connection, $query);

    if (!$result) {
        // die('No result.');
        return false;
    }

    if (mysqli_num_rows($result) == 0) {
        return false;
    }

    $results = [];
    while($row = mysqli_fetch_assoc($result))
    {
        $rowArray = [
            'id' => $row['row_id'],
            'time' => [
                'date' => $row['row_time'],
            ],
            'user' => [
                'id' => $row['user_id'],
                'displayName' => $row['user_displayName'],
            ],
            'type' => [
                'name' => $row['type_name'],
            ],
        ];
        if (isset($row['message_value'])) {
            $rowArray['message'] = [
                'value' => $row['message_value'],
            ];
        }
        if (isset($row['roll_sides']) && isset($row['roll_values'])) {
            foreach (explode(',', $row['roll_values']) as $roll) {
                $rowArray['rolls'][] = [
                    'value' => $roll,
                    'sides' => $row['roll_sides'],
                ];
            }
        }
        $results[] = $rowArray;
    }

    mysqli_close($connection);

    return json_encode($results);
}

function watchForNewRows($watchSettings, $dbSettings, $roomId, $last, $seconds) {
    $newRows = getNewRows($dbSettings, $roomId, $last);
    if (!$newRows) {
        if ($seconds > $watchSettings['timeout']) {
            die();
        }
        sleep($watchSettings['increment']);
        watchForNewRows($watchSettings, $dbSettings, $roomId, $last, $seconds + $watchSettings['increment']);
    }

    echo $newRows;
    exit;
}

watchForNewRows($watchSettings, $dbSettings, $roomId, $last, 0);
