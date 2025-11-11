<?php

namespace App\Contracts;

interface AIModelInterface
{
    /**
     * Generate a SuiteQL query from natural language and table names
     *
     * @param string $userPrompt
     * @param array $tableNames
     * @return string
     */
    public function generateQuery(string $userPrompt, array $tableNames): string;

    /**
     * Extract and format relevant information from query results
     *
     * @param string $userPrompt
     * @param string $query
     * @param array $results
     * @return string
     */
    public function formatResponse(string $userPrompt, string $query, array $results): string;

    /**
     * Check if the AI model is available
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Get the model name
     *
     * @return string
     */
    public function getModelName(): string;
}

