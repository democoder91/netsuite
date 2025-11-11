<?php

namespace App\Services;

use App\Contracts\AIModelInterface;
use App\Http\Controllers\NetSuiteController;
use App\Services\AI\LocalAIModel;
use Exception;
use Illuminate\Support\Facades\Log;

class NetSuiteAIService
{
    protected NetSuiteController $netsuiteController;
    protected AIModelInterface $aiModel;

    public function __construct()
    {
        $this->netsuiteController = new NetSuiteController();
        $this->aiModel = $this->getAIModel();
    }

    /**
     * Process a natural language question and return NetSuite data
     *
     * @param string $userPrompt
     * @return array
     */
    public function askQuestion(string $userPrompt): array
    {
        try {
            // Step 1: Get available table names from NetSuite
            Log::info('Fetching NetSuite table names');
            $tableNames = $this->getTableNames();

            if (empty($tableNames)) {
                throw new Exception('No tables found in NetSuite');
            }

            Log::info('Found tables', ['count' => count($tableNames), 'tables' => $tableNames]);

            // Step 2: Generate SuiteQL query using AI
            Log::info('Generating query with AI', ['prompt' => $userPrompt]);
            $query = $this->aiModel->generateQuery($userPrompt, $tableNames);

            Log::info('Generated query', ['query' => $query]);

            // Step 3: Execute the query against NetSuite
            $data = $this->netsuiteController->sendSuiteQLQuery($query);

            Log::info('Query executed successfully', ['result_count' => $data['count'] ?? 0]);

            // Step 4: Format the response with AI
            $formattedResponse = $this->aiModel->formatResponse($userPrompt, $query, $data);

            return [
                'success' => true,
                'question' => $userPrompt,
                'ai_response' => $formattedResponse,
                'generated_query' => $query,
                'raw_data' => $data,
                'ai_model' => $this->aiModel->getModelName(),
                'tables_available' => count($tableNames)
            ];

        } catch (Exception $e) {
            Log::error('NetSuiteAIService error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'ai_model' => $this->aiModel->getModelName()
            ];
        }
    }

    /**
     * Get available table names from NetSuite
     *
     * @return array
     * @throws Exception
     */
    protected function getTableNames(): array
    {
        // Query NetSuite for custom record types
        $query = "SELECT id, name FROM ScriptCustomRecordType FETCH FIRST 500 ROWS ONLY";

        try {
            $result = $this->netsuiteController->sendSuiteQLQuery($query);
            $items = $result['items'] ?? [];

            $tableNames = [];
            foreach ($items as $item) {
                $name = $item['name'] ?? '';
                if ($name) {
                    // Convert "Accounting Period" to "AccountingPeriod"
                    $tableName = $this->formatTableName($name);
                    $tableNames[] = $tableName;
                }
            }

            // Add common built-in tables
            $builtInTables = [
                'customer',
                'vendor',
                'transaction',
                'item',
                'employee',
                'account',
                'department',
                'location',
                'subsidiary'
            ];

            $tableNames = array_merge($tableNames, $builtInTables);

            return array_unique($tableNames);

        } catch (Exception $e) {
            Log::error('Failed to fetch table names', ['error' => $e->getMessage()]);

            // Fallback to basic tables if query fails
            return [
                'customer',
                'vendor',
                'transaction',
                'item',
                'employee',
                'account'
            ];
        }
    }

    /**
     * Format table name from NetSuite format to SuiteQL format
     *
     * @param string $name
     * @return string
     */
    protected function formatTableName(string $name): string
    {
        // Remove special characters
        $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $name);

        // Convert "Accounting Period" to "AccountingPeriod"
        $words = explode(' ', $name);
        $formatted = implode('', array_map('ucfirst', $words));

        return $formatted;
    }

    /**
     * Get the configured AI model
     *
     * @return AIModelInterface
     */
    protected function getAIModel(): AIModelInterface
    {
        $driver = config('ai.default', 'local');

        return match($driver) {
            'local' => new LocalAIModel(),
            // 'openai' => new OpenAIModel(),
            // 'anthropic' => new AnthropicModel(),
            default => new LocalAIModel(),
        };
    }

    /**
     * Get schema information for a table
     *
     * @param string $tableName
     * @return array
     */
    public function getTableSchema(string $tableName): array
    {
        $query = "SELECT columnname, datatype, nullable FROM syscolumn WHERE tablename = '{$tableName}' ORDER BY columnid FETCH FIRST 100 ROWS ONLY";

        try {
            $data = $this->netsuiteController->sendSuiteQLQuery($query);
            return [
                'success' => true,
                'table' => $tableName,
                'columns' => $data['items'] ?? []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * List all available tables
     *
     * @return array
     */
    public function listTables(): array
    {
        try {
            $tableNames = $this->getTableNames();
            return [
                'success' => true,
                'count' => count($tableNames),
                'tables' => $tableNames
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if AI model is available
     *
     * @return array
     */
    public function checkAIStatus(): array
    {
        return [
            'available' => $this->aiModel->isAvailable(),
            'model' => $this->aiModel->getModelName(),
            'driver' => config('ai.default')
        ];
    }
}

