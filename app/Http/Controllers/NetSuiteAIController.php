<?php

namespace App\Http\Controllers;

use App\Services\NetSuiteAIService;
use Illuminate\Http\Request;

class NetSuiteAIController extends Controller
{
    protected $aiService;

    public function __construct(NetSuiteAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Ask a natural language question about NetSuite data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ask(Request $request)
    {
        $question = $request->input('question') ?? $request->input('prompt');

        if (!$question) {
            return response()->json([
                'error' => 'Question or prompt parameter is required'
            ], 400);
        }

        $result = $this->aiService->askQuestion($question);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }

    /**
     * Get schema for a specific table
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchema(Request $request)
    {
        $table = $request->input('table');

        if (!$table) {
            return response()->json([
                'error' => 'Table parameter is required'
            ], 400);
        }

        $result = $this->aiService->getTableSchema($table);

        return response()->json($result);
    }

    /**
     * List all available tables
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listTables(Request $request)
    {
        $result = $this->aiService->listTables();

        return response()->json($result);
    }

    /**
     * Check AI model status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        $result = $this->aiService->checkAIStatus();

        return response()->json($result);
    }
}

