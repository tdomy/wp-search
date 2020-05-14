<?php

namespace App\Service;

use DateTime;
use PDO;

class PostService
{
    private $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param DateTime $target
     * @return array
     */
    public function findByPostDate(DateTime $target): array
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', $target->format('Y-m-d 00:00:00'));
        $to = DateTime::createFromFormat('Y-m-d H:i:s', (clone $target)->modify('+ 1day')->format('Y-m-d 00:00:00'));

        return $this->fetchByPostDate($from, $to);
    }

    /**
     * @param DateTime $from
     * @param DateTime $to
     * @return array
     */
    private function fetchByPostDate(DateTime $from, DateTime $to): array
    {
        $sql = <<< SQL
SELECT
    id,
    post_date,
    post_title
FROM
    wp_posts
WHERE
    post_date >= :from_datetime
AND
    post_date < :to_datetime
ORDER BY
    post_date desc
;
SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':from_datetime', $from->format('Y-m-d H:i:s'));
        $stmt->bindValue(':to_datetime', $to->format('Y-m-d H:i:s'));
        $stmt->execute();

        return $stmt->fetchAll();
    }
}