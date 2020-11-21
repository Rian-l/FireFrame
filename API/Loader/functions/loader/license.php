<?php
namespace loader;

/* 

0 - license doesnt exist

*/

function get_license_info($connection, $license,$loader_key,$owner) {
    $query = $connection->query('SELECT * loader_licenses WHERE code=? AND owner=? AND loader_key=?',[$license,$owner,$loader_key]);

    if ($query->num_rows === 0)
        return 0;

    $row_data = $query->fetch_assoc();

    $connection->query('DELETE FROM loader_licenses WHERE code=? AND owner=? AND loader_key=?',[$license,$owner,$loader_key]);

    return [
        'usergroup' => $row_data['usergroup'],
        'duration' => $row_data['duration']
    ];
}

?>