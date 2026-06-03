<?php
use PDO;
class VisitorCounter
{
    private PDO $pdo;
    private string $visitorId;
    
    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->visitorId = $this->getOrCreateVisitorId();
    }
    
    /**
     * Récupère ou crée un ID unique pour le visiteur
     */
    private function getOrCreateVisitorId(): string
    {
        session_start();
        
        if (!isset($_SESSION['visitor_id'])) {
            // Générer un ID unique basé sur IP + User Agent + temps
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $_SESSION['visitor_id'] = hash('sha256', $ip . $ua . uniqid('', true));
        }
        
        return $_SESSION['visitor_id'];
    }
    
    /**
     * Enregistre la visite courante
     */
    public function trackVisit(string $pageUrl = ''): void
    {
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        
        // Vérifier si le visiteur existe déjà
        $stmt = $this->pdo->prepare("
            SELECT id, visit_count FROM visitors WHERE visitor_id = ?
        ");
        $stmt->execute([$this->visitorId]);
        $visitor = $stmt->fetch();
        
        if ($visitor) {
            // Mettre à jour le visiteur existant
            $stmt = $this->pdo->prepare("
                UPDATE visitors 
                SET last_visit = ?, visit_count = visit_count + 1
                WHERE visitor_id = ?
            ");
            $stmt->execute([$now, $this->visitorId]);
        } else {
            // Nouveau visiteur
            $stmt = $this->pdo->prepare("
                INSERT INTO visitors (visitor_id, first_visit, last_visit, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $this->visitorId,
                $now,
                $now,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        }
        
        // Enregistrer la page view
        $stmt = $this->pdo->prepare("
            INSERT INTO page_views (visitor_id, page_url, visit_time)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$this->visitorId, $pageUrl ?: $_SERVER['REQUEST_URI'], $now]);
        
        // Mettre à jour les stats journalières
        $this->updateDailyStats($today);
    }
    
    /**
     * Met à jour les statistiques journalières
     */
    private function updateDailyStats(string $date): void
    {
        // Compter les visiteurs uniques du jour
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT visitor_id) as unique_count, COUNT(*) as view_count
            FROM page_views
            WHERE DATE(visit_time) = ?
        ");
        $stmt->execute([$date]);
        $stats = $stmt->fetch();
        
        // Insérer ou mettre à jour
        $stmt = $this->pdo->prepare("
            INSERT INTO daily_stats (visit_date, unique_visitors, page_views)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
            unique_visitors = ?, page_views = ?
        ");
        $stmt->execute([
            $date,
            $stats['unique_count'],
            $stats['view_count'],
            $stats['unique_count'],
            $stats['view_count']
        ]);
    }
    
    /**
     * Récupère le nombre total de visiteurs uniques (tous temps)
     */
    public function getTotalUniqueVisitors(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM visitors");
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Récupère les visiteurs uniques d'aujourd'hui
     */
    public function getTodayUniqueVisitors(): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT visitor_id) as total 
            FROM page_views 
            WHERE DATE(visit_time) = CURDATE()
        ");
        $stmt->execute();
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Récupère les pages vues aujourd'hui
     */
    public function getTodayPageViews(): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total 
            FROM page_views 
            WHERE DATE(visit_time) = CURDATE()
        ");
        $stmt->execute();
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Récupère les visiteurs en ligne (5 dernières minutes)
     */
    public function getOnlineVisitors(): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT visitor_id) as total 
            FROM page_views 
            WHERE visit_time >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ");
        $stmt->execute();
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Récupère les statistiques des 7 derniers jours
     */
    public function getLast7DaysStats(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT visit_date, unique_visitors, page_views
            FROM daily_stats
            WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER BY visit_date ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère le top 10 des pages les plus visitées
     */
    public function getTopPages(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT page_url, COUNT(*) as views
            FROM page_views
            GROUP BY page_url
            ORDER BY views DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}