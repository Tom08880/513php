<?php
/**
 * Helper functions for churn analysis
 */

class ChurnAnalysis {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function calculateChurnRisk($daysSinceLastOrder, $totalOrders) {
        if ($totalOrders == 0) {
            return [
                'risk' => 'No Orders',
                'level' => 'danger',
                'score' => 100
            ];
        }
        
        if ($daysSinceLastOrder > 90) {
            return [
                'risk' => 'High Risk',
                'level' => 'danger',
                'score' => 90
            ];
        } elseif ($daysSinceLastOrder > 60) {
            return [
                'risk' => 'Medium Risk',
                'level' => 'warning',
                'score' => 70
            ];
        } elseif ($daysSinceLastOrder > 30) {
            return [
                'risk' => 'Low Risk',
                'level' => 'info',
                'score' => 40
            ];
        } else {
            return [
                'risk' => 'Active',
                'level' => 'success',
                'score' => 10
            ];
        }
    }
    
    public function getHighRiskCustomers($limit = 50) {
        $query = "
        SELECT 
            s.id as subscriber_id,
            s.first_name,
            s.last_name,
            s.email,
            COUNT(o.order_id) as total_orders,
            MAX(o.created_at) as last_order_date,
            DATEDIFF(CURDATE(), MAX(o.created_at)) as days_since_last_order
        FROM wpv3_fc_subscribers s
        LEFT JOIN orders o ON s.id = o.user_id
        GROUP BY s.id, s.first_name, s.last_name, s.email
        HAVING (total_orders > 0 AND days_since_last_order > 60) OR total_orders = 0
        ORDER BY days_since_last_order DESC
        LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getChurnRate() {
        $query = "
        SELECT 
            COUNT(DISTINCT s.id) as total_customers,
            SUM(CASE WHEN DATEDIFF(CURDATE(), MAX(o.created_at)) > 90 THEN 1 ELSE 0 END) as high_risk,
            SUM(CASE WHEN DATEDIFF(CURDATE(), MAX(o.created_at)) BETWEEN 60 AND 90 THEN 1 ELSE 0 END) as medium_risk
        FROM wpv3_fc_subscribers s
        LEFT JOIN orders o ON s.id = o.user_id
        GROUP BY s.id
        ";
        
        $stmt = $this->db->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['total_customers'] > 0) {
            $totalAtRisk = $result['high_risk'] + $result['medium_risk'];
            return ($totalAtRisk / $result['total_customers']) * 100;
        }
        
        return 0;
    }
    
    public function generateRecommendations($highRiskCount, $mediumRiskCount, $churnRate) {
        $recommendations = [];
        
        if ($highRiskCount > 0) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Win-back Campaign',
                'description' => "Launch targeted email campaign for $highRiskCount high-risk customers",
                'action' => 'Send personalized offers with 20% discount'
            ];
        }
        
        if ($mediumRiskCount > 0) {
            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'Re-engagement Program',
                'description' => "Implement re-engagement strategy for $mediumRiskCount medium-risk customers",
                'action' => 'Send product updates and loyalty rewards'
            ];
        }
        
        if ($churnRate > 20) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Customer Retention Program',
                'description' => 'Churn rate above 20% requires immediate action',
                'action' => 'Implement customer feedback system and improve service'
            ];
        }
        
        return $recommendations;
    }
}
?>