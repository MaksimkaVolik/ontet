<?php
namespace App\Models;

use Core\Database;
use Core\Exceptions\DatabaseException;

class Referral {
    private Database $db;
    private const MAX_LEVEL = 3;
    private const COMMISSION_RATES = [0.3, 0.15, 0.05];

    public function __construct() {
        $this->db = new Database();
    }

    public function addReferral(int $referrerId, int $referredId): void {
        $this->db->beginTransaction();
        
        try {
            // Добавляем прямую реферальную связь (1 уровень)
            $this->db->query(
                "INSERT INTO user_referrals 
                 (referrer_id, referred_id, level)
                 VALUES (:referrer_id, :referred_id, 1)",
                [
                    'referrer_id' => $referrerId,
                    'referred_id' => $referredId
                ]
            );

            // Добавляем связи для вышестоящих реферреров
            $upperReferrers = $this->db->query(
                "SELECT referrer_id, level 
                 FROM user_referrals 
                 WHERE referred_id = :referrer_id
                 AND level < :max_level",
                [
                    'referrer_id' => $referrerId,
                    'max_level' => self::MAX_LEVEL
                ]
            )->fetchAll();

            foreach ($upperReferrers as $ref) {
                $this->db->query(
                    "INSERT INTO user_referrals 
                     (referrer_id, referred_id, level)
                     VALUES (:referrer_id, :referred_id, :level)",
                    [
                        'referrer_id' => $ref['referrer_id'],
                        'referred_id' => $referredId,
                        'level' => $ref['level'] + 1
                    ]
                );
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new DatabaseException("Failed to add referral: " . $e->getMessage());
        }
    }

    public function processReferralPayout(int $conversionId): void {
        $conversion = $this->db->query(
            "SELECT c.amount, oc.partner_id 
             FROM offer_conversions c
             JOIN offer_clicks oc ON c.click_id = oc.id
             WHERE c.id = :id AND c.status = 'approved'",
            ['id' => $conversionId]
        )->fetch();

        if (!$conversion) return;

        $referrers = $this->db->query(
            "SELECT ur.referrer_id, ur.level
             FROM user_referrals ur
             WHERE ur.referred_id = :user_id
             AND ur.level <= :max_level",
            [
                'user_id' => $conversion['partner_id'],
                'max_level' => self::MAX_LEVEL
            ]
        )->fetchAll();

        foreach ($referrers as $ref) {
            $amount = $conversion['amount'] * self::COMMISSION_RATES[$ref['level'] - 1];
            
            $this->db->query(
                "INSERT INTO referral_payments
                 (referrer_id, amount, level, conversion_id, status)
                 VALUES (:referrer_id, :amount, :level, :conversion_id, 'pending')",
                [
                    'referrer_id' => $ref['referrer_id'],
                    'amount' => $amount,
                    'level' => $ref['level'],
                    'conversion_id' => $conversionId
                ]
            );
        }
    }
}