<?php
// loss.php - Mission Failed Page (Time's Up)
require_once 'db.php';

$game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$game = null;
$player_string = "";
$time_used_str = "60:00"; // Default fallback
$puzzles_solved = "3/5";
$progress_str = "60%";
$custom_fail_msg = "You ran out of time before completing all the puzzles. The enemy has been alerted to your presence.";

if ($game_id > 0) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT g.*, s.player1_name, s.player2_name 
            FROM `games` g 
            JOIN `sessions` s ON g.session_id = s.id 
            WHERE g.id = ? AND g.status = 'lost'
        ");
        $stmt->execute([$game_id]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($game) {
            $player_string = htmlspecialchars($game['player1_name']);
            if (!empty($game['player2_name'])) {
                $player_string .= " & " . htmlspecialchars($game['player2_name']);
            }
            
            // Time Used (always maxed at 60 mins for a full timeout, or elapsed time if failed early)
            $spent = intval($game['time_spent']);
            $m = floor($spent / 60);
            $s = $spent % 60;
            $time_used_str = sprintf("%02d:%02d", $m, $s);
            
            $puzzles_solved = $game['puzzles_solved'] . "/" . $game['total_puzzles'];
            
            $pct = round(($game['puzzles_solved'] / $game['total_puzzles']) * 100);
            $progress_str = $pct . "%";
            
            // Set customized room failure messages
            if (strpos($game['room_name'], 'War') !== false) {
                $custom_fail_msg = "You ran out of time before disarming the booby traps. The enemy has breached the perimeter and captured your squad.";
            } else {
                $custom_fail_msg = "Oxygen reserves depleted. The spacecraft drift log reports total life support failure inside the cosmic anomaly.";
            }
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
    <title>Time's Up - Escape Room</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="state-loss">

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
            
            <!-- X Icon Circle -->
            <div class="result-icon-outer">
                <svg viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>

            <!-- Headings -->
            <h1 class="result-heading">[TIME'S UP]</h1>
            <p class="result-subheading">[Mission Failed]</p>

            <!-- Message Box -->
            <div class="result-msg-box">
                [<?= htmlspecialchars($custom_fail_msg) ?>]
            </div>

            <!-- Stats Box -->
            <div class="result-box">
                <?php if ($game): ?>
                    <div style="font-family: 'Share Tech Mono', monospace; font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0.8rem;">
                        Clearance: <?= $player_string ?> | Room: <?= htmlspecialchars($game['room_name']) ?>
                    </div>
                <?php endif; ?>
                
                <div class="stat-row">
                    <span class="stat-row-lbl">[Time Used:]</span>
                    <span class="stat-row-val">[<?= $time_used_str ?>]</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-lbl">[Puzzles Solved:]</span>
                    <span class="stat-row-val">[<?= $puzzles_solved ?>]</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-lbl">[Progress:]</span>
                    <span class="stat-row-val" style="color: var(--danger-red);">[<?= $progress_str ?>]</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="btn-row">
                <a href="index.php" class="btn btn-outline">[TRY AGAIN]</a>
                <a href="index.php" class="btn btn-danger">[MAIN MENU]</a>
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
