<?php
    require_once realpath(__DIR__."/../DBConn.php");

    //Get the connection from the interface.
    $conn = DBConn::getInstance()->getConnection();

    $chatsQ = <<<SQL
    CREATE TABLE `chats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(32) NOT NULL,
    `username` varchar(32) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    SQL;

    $chatsContentsQ = <<<SQL
    CREATE TABLE `chatscontents` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `chatId` int(11) NOT NULL,
    `message` text NOT NULL,
    `response` text NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    SQL;
    
    $tasksQ = <<<SQL
    CREATE TABLE `tasks` (
    `id` int(11) NOT NULL,
    `startTime` int(11) NOT NULL,
    `endTime` int(11) NOT NULL,
    `title` text NOT NULL,
    `completed` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    SQL;

    $token_black_listQ = <<<SQL
    CREATE TABLE `token_black_list` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `token` text NOT NULL,
    `expiration` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    SQL;

    $usersQ = <<<SQL
    CREATE TABLE `users` (
    `username` varchar(32) NOT NULL,
    `hashPass` text NOT NULL,
    `encEmail` text DEFAULT NULL,
    `subscription` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subscription`)),
    PRIMARY KEY (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    SQL;

    // Array of SQL queries
    $sqlQueries = [
        $chatsQ,
        $chatsContentsQ,
        $tasksQ,
        $token_black_listQ,
        $usersQ
    ];

    // Check if tables exist and drop them if they do
    foreach ($sqlQueries as $query) {
        $tableName = getTableName($query);
        $checkTableQuery = "SHOW TABLES LIKE '$tableName'";
        $result = $conn->query($checkTableQuery);

        if ($result && $result->num_rows > 0) {
            // Table exists, drop it
            $dropTableQuery = "DROP TABLE IF EXISTS $tableName";
            $conn->query($dropTableQuery);
        }
    }

    // Create tables
    foreach ($sqlQueries as $query) {
        if ($conn->query($query) === TRUE) {
            echo "Table created successfully ".getTableName($query)."\n";
        } else {
            echo "Error creating table: " . $conn->error . "\n";
        }
    }

    // Function to extract table name from SQL query
    function getTableName($sqlQuery)
    {
        preg_match('/CREATE TABLE `(.+?)`/', $sqlQuery, $matches);
        return $matches[1];
    }
?>