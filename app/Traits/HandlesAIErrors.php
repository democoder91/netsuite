<?php

namespace App\Traits;

trait HandlesAIErrors
{
    /**
     * Handle AI model errors gracefully
     *
     * @param \Exception $e
     * @return array
     */
    protected function handleAIError(\Exception $e): array
    {
        return [
            'success' => false,
            'error' => 'AI Model Error: ' . $e->getMessage(),
            'model' => $this->getModelName()
        ];
    }

    /**
     * Validate AI response
     *
     * @param mixed $response
     * @return bool
     */
    protected function validateResponse($response): bool
    {
        return !empty($response) && is_string($response);
    }

    /**
     * Clean SQL query from markdown or extra formatting
     *
     * @param string $query
     * @return string
     */
    protected function cleanQuery(string $query): string
    {
        // Remove markdown code blocks
        $query = preg_replace('/```sql\n?/', '', $query);
        $query = preg_replace('/```\n?/', '', $query);

        // Remove extra whitespace
        $query = trim($query);

        // Remove trailing semicolons if present
        $query = rtrim($query, ';');

        return $query;
    }
}

