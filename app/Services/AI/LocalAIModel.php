<?php

namespace App\Services\AI;

use App\Contracts\AIModelInterface;
use App\Traits\HandlesAIErrors;
use Illuminate\Support\Facades\Http;
use Exception;

class LocalAIModel implements AIModelInterface
{
    use HandlesAIErrors;

    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('ai.local.base_url', 'http://localhost:11434');
        $this->model = config('ai.local.model', 'mistral');
    }

    /**
     * Generate a SuiteQL query from natural language and table names
     *
     * @param string $userPrompt
     * @param array $tableNames
     * @return string
     * @throws Exception
     */
    public function generateQuery(string $userPrompt, array $tableNames): string
    {
        $systemPrompt = $this->buildQueryGenerationPrompt($tableNames);

        $response = $this->callLocalAI($systemPrompt, $userPrompt);

        if (!$this->validateResponse($response)) {
            throw new Exception('Invalid response from AI model');
        }

        return $this->cleanQuery($response);
    }

    /**
     * Extract and format relevant information from query results
     *
     * @param string $userPrompt
     * @param string $query
     * @param array $results
     * @return string
     * @throws Exception
     */
    public function formatResponse(string $userPrompt, string $query, array $results): string
    {
        $systemPrompt = "You are a helpful assistant that explains NetSuite data clearly and concisely. Format your response in a readable way.";

        $dataJson = json_encode($results, JSON_PRETTY_PRINT);

        $prompt = "The user asked: \"{$userPrompt}\"\n\n";
        $prompt .= "I executed this SuiteQL query: {$query}\n\n";
        $prompt .= "Here's the data returned from NetSuite:\n{$dataJson}\n\n";
        $prompt .= "Please provide a clear, human-readable summary that answers the user's question. ";
        $prompt .= "If there are multiple records, summarize key findings. Format numbers appropriately.";

        $response = $this->callLocalAI($systemPrompt, $prompt);

        if (!$this->validateResponse($response)) {
            throw new Exception('Invalid response from AI model');
        }

        return $response;
    }

    /**
     * Check if the AI model is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/api/tags');
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get the model name
     *
     * @return string
     */
    public function getModelName(): string
    {
        return 'Local AI (' . $this->model . ')';
    }

    /**
     * Call the local AI API
     *
     * @param string $systemPrompt
     * @param string $userPrompt
     * @return string
     * @throws Exception
     */
    protected function callLocalAI(string $systemPrompt, string $userPrompt): string
    {
        try {
            $response = Http::timeout(60)->post($this->baseUrl . '/api/generate', [
                'model' => $this->model,
                'prompt' => $systemPrompt . "\n\nUser: " . $userPrompt . "\n\nAssistant:",
                'stream' => false,
            ]);

            if (!$response->successful()) {
                throw new Exception('AI API request failed: ' . $response->body());
            }

            $data = $response->json();
            return $data['response'] ?? '';

        } catch (Exception $e) {
            throw new Exception('Failed to communicate with local AI: ' . $e->getMessage());
        }
    }

    /**
     * Build the system prompt for query generation
     *
     * @param array $tableNames
     * @return string
     */
    protected function buildQueryGenerationPrompt(array $tableNames): string
    {
        $tableList = implode(', ', $tableNames);

        return <<<PROMPT
You are a NetSuite SuiteQL expert. Convert natural language questions into valid SuiteQL queries.

IMPORTANT RULES:
1. Use "FETCH FIRST n ROWS ONLY" instead of "LIMIT n"
2. Only use tables from the available list provided
3. Column names are often lowercase and concatenated (companyname, tranid, trandate, etc.)
4. Always use proper SQL syntax with SELECT, FROM, WHERE, JOIN as needed
5. For transactions, use the 'type' field to filter (e.g., type = 'SalesOrd' for sales orders)
6. Date comparisons use TO_DATE('YYYY-MM-DD', 'YYYY-MM-DD') format
7. Return ONLY the SQL query, no explanations or markdown
8. If user asks for "all" or doesn't specify a limit, use FETCH FIRST 100 ROWS ONLY

AVAILABLE TABLES:
{$tableList}

Common column patterns:
- Most tables have: id, name
- customer table: id, companyname, email, phone
- transaction table: id, tranid, trandate, entity, type, status, amount
- item table: id, itemid, displayname, itemtype

Examples:
Q: "Show me the top 5 customers"
A: SELECT id, companyname, email FROM customer FETCH FIRST 5 ROWS ONLY

Q: "What are the accounting periods?"
A: SELECT id, name FROM AccountingPeriod FETCH FIRST 20 ROWS ONLY

Now convert the user's question into a valid SuiteQL query using ONLY the available tables.
PROMPT;
    }
}

