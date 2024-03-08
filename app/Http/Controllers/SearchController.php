<?php

namespace App\Http\Controllers;

use App\Core\DatabaseConnection;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SearchController
{
    const ABBREVIATIONS = [
        'г.', 'город', 'г',
        'ул.', 'улица', 'ул',
        'пер.', 'переулок', 'пер',
        'пр-т', 'проспект', 'пр-т', 'просп',
        'б-р', 'бульвар', 'б-р', 'бул',
        'д.', 'деревня', 'дер',
        'х.', 'хутор', 'хут',
        'ст.', 'станица', 'стн',
        'а.', 'аул',
        'пр-д', 'проезд', 'пр',
        'ал.', 'аллея', 'алл',
        'наб.', 'набережная', 'наб',
        'пл.', 'площадь',
        'туп.', 'тупик', 'туп',
        'прлк.', 'проулок',
        'кв.', 'квартал', 'кварт',
        'мкр.', 'микрорайон', 'мкрн',
        'жк', 'жилой комплекс',
        'тер.', 'территория', 'терр',
        'п.', 'поселок', 'пос',
        'с.', 'село', 'сел',
        'р. п.', 'рабочий поселок', 'рп',
        'н. п.', 'населенный пункт', 'нп',
        'ш.', 'шоссе', 'шос',
    ];

    public function search(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $searchQuery = $queryParams['query'] ?? '';

        $dbConnection = DatabaseConnection::getInstance()->getConnection();

        $stmt = $dbConnection->prepare(
            "SELECT * FROM addresses 
            WHERE 
                context ILIKE :raw_query OR (
                    context ILIKE :filtered_query AND 
                    house = '*'
                )
            ORDER BY context LIMIT 5"
        );
        $searchQuery = $this->getRawSearchString($searchQuery);
        $filteredSearchQuery = $this->getFilteredSearchString($searchQuery);
        $stmt->execute(['raw_query' => "%{$searchQuery}%", 'filtered_query' => "%{$filteredSearchQuery}%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert results to JSON
        $response->getBody()->write(json_encode(array_map(fn ($item) => [
            'branch_id' => $item['branch_id'],
            'address' => $item['display_address'],
        ], $results)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getRawSearchString(string $searchString): string
    {
        $combinations = [];
        $suggestion = mb_strtolower($searchString);

        $parts = explode(',', $suggestion);
        foreach ($parts as $part) {
            $smallParts = explode(' ', $part);
            foreach ($smallParts as $smallPart) {
                if (!in_array($smallPart, self::ABBREVIATIONS) && $smallPart != '') {
                    $combinations[] = $smallPart;
                }
            }
        }

        return implode(' ', $combinations);
    }

    private function getFilteredSearchString(string $searchString): string
    {
        $words = explode(' ', $searchString);
        $lastWord = end($words);

        if (preg_match('/\d/', $lastWord)) {
            array_pop($words);
            return implode(' ', $words);
        }
        return $searchString;
    }
}
