<?php
// win.php - Mission Accomplished Page
require_once 'db.php';

$game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$game = null;
$player_string = "";
$time_remaining_str = "12:34"; // Default fallback
$puzzles_solved = "5/5";
$hints_used = "2";
$score_str = "9,450 pts";

if ($game_id > 0) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT g.*, s.player1_name, s.player2_name 
            FROM `games` g 
            JOIN `sessions` s ON g.session_id = s.id 
            WHERE g.id = ? AND g.status = 'won'
        ");
        $stmt->execute([$game_id]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($game) {
            $player_string = htmlspecialchars($game['player1_name']);
            if (!empty($game['player2_name'])) {
                $player_string .= " & " . htmlspecialchars($game['player2_name']);
            }
            
            // Calculate time remaining (60 minutes = 3600 seconds)
            $remaining_sec = 3600 - intval($game['time_spent']);
            if ($remaining_sec < 0) $remaining_sec = 0;
            
            $m = floor($remaining_sec / 60);
            $s = $remaining_sec % 60;
            $time_remaining_str = sprintf("%02d:%02d", $m, $s);
            
            $puzzles_solved = $game['puzzles_solved'] . "/" . $game['total_puzzles'];
            $hints_used = $game['hints_used'];
            $score_str = number_format($game['score']) . " pts";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission Accomplished - Escape Room</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="state-win">

    <!-- Header Navigation -->
    <header class="site-header">
        <nav class="header-nav">
            <a href="index.php" class="nav-brand">
                <span style="color: var(--war-gold);">[</span> ESCAPE ROOM <span style="color: var(--space-purple);">]</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">MISSION BRIEFING</a></li>
                <li><a href="leaderboard.php">LEADERBOARD</a></li>
                <li><a href="review.php">REVIEWS</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Container -->
    <main class="container flex-center" style="flex-grow: 1;">
        <div class="result-card-container">
            
            <!-- Flag Icon Circle -->
            <div class="result-icon-outer">
                <svg viewBox="0 0 24 24">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
            </div>

            <!-- Headings -->
            <h1 class="result-heading">[MISSION COMPLETE]</h1>
            <p class="result-subheading">[Congratulations! You escaped!]</p>

            <!-- Stats Box -->
            <div class="result-box">
                <?php if ($game): ?>
                    <div style="font-family: 'Share Tech Mono', monospace; font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0.8rem;">
                        Clearance: <?= $player_string ?> | Room: <?= htmlspecialchars($game['room_name']) ?>
                    </div>
                <?php endif; ?>
                
                <div class="stat-row">
                    <span class="stat-row-lbl">[Time Remaining:]</span>
                    <span class="stat-row-val">[<?= $time_remaining_str ?>]</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-lbl">[Puzzles Solved:]</span>
                    <span class="stat-row-val">[<?= $puzzles_solved ?>]</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-lbl">[Hints Used:]</span>
                    <span class="stat-row-val">[<?= $hints_used ?>]</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-lbl">[Score:]</span>
                    <span class="stat-row-val" style="color: var(--success-green);">[<?= $score_str ?>]</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="btn-row">
                <a href="index.php" class="btn btn-outline">[PLAY AGAIN]</a>
                <a href="leaderboard.php" class="btn btn-primary">[LEADERBOARD]</a>
            </div>

        </div>
    </main>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container" style="padding: 0 2rem;">
            <p class="footer-text">&copy; 2026 ESCAPE ROOM SPRINT 1. ALL RIGHTS RESERVED.</p>
        </div>
    </footer>

</body>
</html>
