<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/cache.php';

function ap_fetch_faqs(array $options = []): array
{
    $useCache = empty($options['disable_cache']);
    $normalizedOptions = $options;
    unset($normalizedOptions['disable_cache']);
    if (isset($normalizedOptions['ids']) && is_array($normalizedOptions['ids'])) {
        $normalizedOptions['ids'] = array_values(array_unique(array_map('intval', $normalizedOptions['ids'])));
        sort($normalizedOptions['ids']);
    }
    ksort($normalizedOptions);
    $cacheKey = $useCache ? 'faqs_' . md5(serialize($normalizedOptions)) : null;
    if ($useCache && $cacheKey) {
        $cached = ap_cache_get($cacheKey, 300);
        if ($cached !== null) {
            return $cached;
        }
    }
    $pdo = ap_db();
    $where = [];
    $params = [];

    if (!empty($options['active_only'])) {
        $where[] = 'is_active = 1';
    }

    if (!empty($options['search'])) {
        $where[] = '(LOWER(question) LIKE :search OR LOWER(answer) LIKE :search OR LOWER(IFNULL(keywords, "")) LIKE :search)';
        $params[':search'] = '%' . strtolower($options['search']) . '%';
    }

    if (!empty($options['category'])) {
        $where[] = 'category = :category';
        $params[':category'] = $options['category'];
    }

    if (!empty($options['ids']) && is_array($options['ids'])) {
        $placeholders = [];
        foreach ($options['ids'] as $index => $id) {
            $key = ':faq_id_' . $index;
            $placeholders[] = $key;
            $params[$key] = (int) $id;
        }
        if ($placeholders) {
            $where[] = 'id IN (' . implode(',', $placeholders) . ')';
        }
    }

    $sql = 'SELECT * FROM faqs';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY sort_order ASC, created_at DESC';

    if (!empty($options['limit'])) {
        $limit = max(1, (int) $options['limit']);
        $sql .= ' LIMIT ' . $limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    if ($useCache && $cacheKey) {
        ap_cache_set($cacheKey, $results, 300);
    }
    return $results;
}

function ap_active_faqs(): array
{
    return ap_fetch_faqs(['active_only' => true]);
}

function ap_find_faq(int $id): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM faqs WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $faq = $stmt->fetch();
    return $faq ?: null;
}

function ap_save_faq(array $data, ?int $id = null): int
{
    $pdo = ap_db();
    $question = trim($data['question'] ?? '');
    $answer = trim($data['answer'] ?? '');
    $category = trim($data['category'] ?? '');
    $keywords = trim($data['keywords'] ?? '');
    $sortOrder = isset($data['sort_order']) ? max(0, (int) $data['sort_order']) : 0;
    $isActive = !empty($data['is_active']) ? 1 : 0;

    if ($question === '' || $answer === '') {
        throw new InvalidArgumentException('question_answer_required');
    }

    if ($id) {
        $stmt = $pdo->prepare('UPDATE faqs SET question = :question, answer = :answer, category = :category, keywords = :keywords, sort_order = :sort_order, is_active = :is_active, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            ':question' => $question,
            ':answer' => $answer,
            ':category' => $category !== '' ? $category : null,
            ':keywords' => $keywords !== '' ? $keywords : null,
            ':sort_order' => $sortOrder,
            ':is_active' => $isActive,
            ':id' => $id,
        ]);
        ap_cache_forget_prefix('faqs_');
        return $id;
    }

    $stmt = $pdo->prepare('INSERT INTO faqs (question, answer, category, keywords, sort_order, is_active) VALUES (:question, :answer, :category, :keywords, :sort_order, :is_active)');
    $stmt->execute([
        ':question' => $question,
        ':answer' => $answer,
        ':category' => $category !== '' ? $category : null,
        ':keywords' => $keywords !== '' ? $keywords : null,
        ':sort_order' => $sortOrder,
        ':is_active' => $isActive,
    ]);
    ap_cache_forget_prefix('faqs_');
    return (int) $pdo->lastInsertId();
}

function ap_delete_faq(int $id): void
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('DELETE FROM faqs WHERE id = :id');
    $stmt->execute([':id' => $id]);
    ap_cache_forget_prefix('faqs_');
}