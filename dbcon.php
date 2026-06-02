<?php
// db.php - Database connection and auto-initialization helper

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'escape_room';

try {
    // 1. First connect to MySQL server without selecting database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Create database if it doesn't exist (using backticks)
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 3. Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 4. Create 'sessions' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `sessions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `player1_name` VARCHAR(100) NOT NULL,
        `player2_name` VARCHAR(100) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    
    // 5. Create 'games' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `games` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `session_id` INT NOT NULL,
        `room_name` VARCHAR(100) NOT NULL,
        `status` ENUM('playing', 'won', 'lost') DEFAULT 'playing',
        `puzzles_solved` INT DEFAULT 0,
        `total_puzzles` INT DEFAULT 5,
        `hints_used` INT DEFAULT 0,
        `time_spent` INT DEFAULT 0, -- in seconds
        `score` INT DEFAULT 0,
        `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `ended_at` TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    
    // 6. Create 'reviews' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `reviews` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `player_name` VARCHAR(100) NOT NULL,
        `rating` INT NOT NULL,
        `comment` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    
    // 7. Create 'questions' table (using exact structure from your SQL file)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `questions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `questions` VARCHAR(255) NOT NULL,
        `answer` VARCHAR(100) NOT NULL,
        `hint` VARCHAR(255) DEFAULT NULL,
        `roomId` INT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    
    // 8. Seed default data if tables are empty
    
    // Seed Questions (using exact questions and IDs from your SQL file)
    $qCheck = $pdo->query("SELECT COUNT(*) FROM `questions`")->fetchColumn();
    if ($qCheck == 0) {
        $questions = [
            ['id' => 10, 'questions' => 'Wat is de hoofdstad van Nederland?', 'answer' => 'Amsterdam', 'hint' => 'Het is ook de grootste stad van Nederland.', 'roomId' => 1],
            ['id' => 11, 'questions' => 'Welk getal volgt na 7 in de reeks 5,6,7?', 'answer' => '8', 'hint' => 'Tel gewoon verder.', 'roomId' => 1],
            ['id' => 12, 'questions' => 'Welke kleur krijg je als je rood en geel mengt?', 'answer' => 'Oranje', 'hint' => 'Het is de kleur van een sinaasappel.', 'roomId' => 2],
            ['id' => 13, 'questions' => 'Hoeveel zijden heeft een driehoek?', 'answer' => '3', 'hint' => 'Het aantal hoeken is hetzelfde.', 'roomId' => 2],
            ['id' => 14, 'questions' => 'Wat is het tegenovergestelde van “hoog”?', 'answer' => 'Laag', 'hint' => 'Denk aan iets onder iets anders.', 'roomId' => 2]
            ['id' => 10, 'questions' => 'Wat is de hoofdstad van Eritrea?', 'answer' => 'Asmera' => 'hint' => 'Het is ook de mooiste stad van Eritrea.', 'roomId' => 1],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO `questions` (`id`, `questions`, `answer`, `hint`, `roomId`) VALUES (?, ?, ?, ?, ?)");
        foreach ($questions as $q) {
            $stmt->execute([$q['id'], $q['questions'], $q['answer'], $q['hint'], $q['roomId']]);
        }
    }
    
    // Seed Reviews
    $rCheck = $pdo->query("SELECT COUNT(*) FROM `reviews`")->fetchColumn();
    if ($rCheck == 0) {
        $reviews = [
            ['player_name' => 'Alex', 'rating' => 5, 'comment' => 'The Void Protocol room was absolutely stellar! Beautiful visuals and clever puzzles.'],
            ['player_name' => 'Sarah & John', 'rating' => 4, 'comment' => 'Great atmosphere in Theater of War. The ciphers felt very authentic!'],
            ['player_name' => 'Lucas', 'rating' => 5, 'comment' => 'Highly immersive! Best escape room I have played in a web browser.']
        ];
        $stmt = $pdo->prepare("INSERT INTO `reviews` (`player_name`, `rating`, `comment`) VALUES (?, ?, ?)");
        foreach ($reviews as $r) {
            $stmt->execute([$r['player_name'], $r['rating'], $r['comment']]);
        }
    }
    
    // Seed Sessions & Games (Leaderboard)
    $sCheck = $pdo->query("SELECT COUNT(*) FROM `sessions`")->fetchColumn();
    if ($sCheck == 0) {
        // Seed Session 1 (Winner)
        $pdo->exec("INSERT INTO `sessions` (`player1_name`, `player2_name`) VALUES ('Marcus', 'Elena')");
        $sid1 = $pdo->lastInsertId();
        $pdo->exec("INSERT INTO `games` (`session_id`, `room_name`, `status`, `puzzles_solved`, `total_puzzles`, `hints_used`, `time_spent`, `score`, `ended_at`) 
                    VALUES ($sid1, 'Void Protocol', 'won', 5, 5, 1, 2754, 9150, NOW())"); // ~45 mins spent, 15 mins remaining
                    
        // Seed Session 2 (Winner)
        $pdo->exec("INSERT INTO `sessions` (`player1_name`, `player2_name`) VALUES ('Victor', NULL)");
        $sid2 = $pdo->lastInsertId();
        $pdo->exec("INSERT INTO `games` (`session_id`, `room_name`, `status`, `puzzles_solved`, `total_puzzles`, `hints_used`, `time_spent`, `score`, `ended_at`) 
                    VALUES ($sid2, 'Theater of War', 'won', 5, 5, 2, 2846, 8450, NOW())"); // ~47 mins spent
                    
        // Seed Session 3 (Lost)
        $pdo->exec("INSERT INTO `sessions` (`player1_name`, `player2_name`) VALUES ('Sophie', 'Daniel')");
        $sid3 = $pdo->lastInsertId();
        $pdo->exec("INSERT INTO `games` (`session_id`, `room_name`, `status`, `puzzles_solved`, `total_puzzles`, `hints_used`, `time_spent`, `score`, `ended_at`) 
                    VALUES ($sid3, 'Theater of War', 'lost', 3, 5, 4, 3600, 3000, NOW())"); // ran out of time (3600s = 60 mins)
    }
    
} catch (PDOException $e) {
    // If it fails (e.g. MySQL server not running or wrong credentials), we capture it
    // So the app can degrade gracefully or show a database connection error message.
    $db_error = $e->getMessage();
}

/**
 * Helper function to get PDO instance
 */
function getDB() {
    global $pdo, $db_error;
    if (isset($db_error)) {
        throw new Exception("Database Connection Failed: " . $db_error);
    }
    return $pdo;
}
?>
