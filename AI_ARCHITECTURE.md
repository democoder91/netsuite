# NetSuite AI Agent - Flexible Multi-Model Architecture

## Overview

A flexible AI-powered system that allows you to ask natural language questions about your NetSuite account. The architecture supports multiple AI models through an interface-based design.

## Architecture

### Components

1. **AIModelInterface** - Contract that all AI models must implement
2. **HandlesAIErrors Trait** - Common error handling and query cleaning
3. **LocalAIModel** - First implementation using local AI (Ollama/LM Studio)
4. **NetSuiteAIService** - Orchestrates the entire flow
5. **NetSuiteAIController** - API endpoints

### Flow Diagram

```
User Request (Prompt)
        ↓
NetSuiteAIController
        ↓
NetSuiteAIService
        ↓
┌───────┴────────┐
│ Step 1:        │
│ Get Table Names│  ← SELECT * FROM ScriptCustomRecordType
└───────┬────────┘
        ↓
┌───────┴────────┐
│ Step 2:        │
│ Format Names   │  "Accounting Period" → "AccountingPeriod"
└───────┬────────┘
        ↓
┌───────┴────────┐
│ Step 3:        │
│ AI generates   │  Prompt + Table Names → SuiteQL Query
│ Query          │
└───────┬────────┘
        ↓
┌───────┴────────┐
│ Step 4:        │
│ Execute Query  │  → NetSuite API
└───────┬────────┘
        ↓
┌───────┴────────┐
│ Step 5:        │
│ AI Formats     │  Query Results → Human Response
│ Response       │
└───────┬────────┘
        ↓
Return to User
```

## Setup

### Option 1: Local AI (Ollama)

1. **Install Ollama**
   ```bash
   # Download from: https://ollama.ai
   ```

2. **Pull a model**
   ```bash
   ollama pull llama2
   # or
   ollama pull mistral
   # or
   ollama pull codellama
   ```

3. **Configure .env**
   ```env
   AI_MODEL=local
   LOCAL_AI_URL=http://localhost:11434
   LOCAL_AI_MODEL=llama2
   ```

### Option 2: LM Studio

1. **Install LM Studio** from https://lmstudio.ai

2. **Load a model** (e.g., Mistral, CodeLlama)

3. **Start local server** on port 1234

4. **Configure .env**
   ```env
   AI_MODEL=local
   LOCAL_AI_URL=http://localhost:1234
   LOCAL_AI_MODEL=your-model-name
   ```

### Option 3: OpenAI (Future)

```env
AI_MODEL=openai
OPENAI_API_KEY=sk-your-key-here
OPENAI_MODEL=gpt-4
```

## API Endpoints

### 1. Ask a Question
**POST** `/api/netsuite/ai/ask`

Send a natural language question.

**Request:**
```json
{
  "prompt": "Show me the top 10 customers"
}
```
or
```json
{
  "question": "What are the recent sales orders?"
}
```

**Response:**
```json
{
  "success": true,
  "question": "Show me the top 10 customers",
  "generated_query": "SELECT id, companyname, email FROM customer FETCH FIRST 10 ROWS ONLY",
  "raw_data": {
    "count": 10,
    "items": [...]
  },
  "ai_response": "Here are your top 10 customers:\n1. ABC Corp\n2. XYZ Ltd\n...",
  "ai_model": "Local AI (llama2)",
  "tables_available": 45
}
```

### 2. List Available Tables
**GET** `/api/netsuite/ai/tables`

Get all tables the AI can query.

**Response:**
```json
{
  "success": true,
  "count": 45,
  "tables": [
    "AccountingPeriod",
    "customer",
    "vendor",
    "transaction",
    ...
  ]
}
```

### 3. Get Table Schema
**GET** `/api/netsuite/ai/schema?table=customer`

Get column information for a table.

**Response:**
```json
{
  "success": true,
  "table": "customer",
  "columns": [
    {
      "columnname": "id",
      "datatype": "INTEGER",
      "nullable": "N"
    },
    ...
  ]
}
```

### 4. Check AI Status
**GET** `/api/netsuite/ai/status`

Check if AI model is available.

**Response:**
```json
{
  "available": true,
  "model": "Local AI (llama2)",
  "driver": "local"
}
```

## Configuration Files

### config/ai.php
```php
'default' => env('AI_MODEL', 'local'),

'models' => [
    'local' => [
        'driver' => 'local',
        'base_url' => env('LOCAL_AI_URL', 'http://localhost:11434'),
        'model' => env('LOCAL_AI_MODEL', 'llama2'),
    ],
    'openai' => [
        'driver' => 'openai',
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
    ],
],
```

## Adding New AI Models

To add a new AI model (e.g., Anthropic Claude, Google Gemini):

### Step 1: Create Model Class

```php
namespace App\Services\AI;

use App\Contracts\AIModelInterface;
use App\Traits\HandlesAIErrors;

class AnthropicModel implements AIModelInterface
{
    use HandlesAIErrors;

    public function generateQuery(string $userPrompt, array $tableNames): string
    {
        // Implement API call to Anthropic
    }

    public function formatResponse(string $userPrompt, string $query, array $results): string
    {
        // Implement response formatting
    }

    public function isAvailable(): bool
    {
        // Check if API is accessible
    }

    public function getModelName(): string
    {
        return 'Claude 3 Sonnet';
    }
}
```

### Step 2: Register in config/ai.php

```php
'anthropic' => [
    'driver' => 'anthropic',
    'api_key' => env('ANTHROPIC_API_KEY'),
    'model' => env('ANTHROPIC_MODEL', 'claude-3-sonnet-20240229'),
],
```

### Step 3: Update Service Factory

In `NetSuiteAIService::getAIModel()`:
```php
return match($driver) {
    'local' => new LocalAIModel(),
    'openai' => new OpenAIModel(),
    'anthropic' => new AnthropicModel(), // Add this
    default => new LocalAIModel(),
};
```

## Usage Examples

### cURL
```bash
# Ask a question
curl -X POST http://netsuite.test/api/netsuite/ai/ask \
  -H "Content-Type: application/json" \
  -d '{"prompt":"Show me customers from this year"}'

# Check status
curl http://netsuite.test/api/netsuite/ai/status

# List tables
curl http://netsuite.test/api/netsuite/ai/tables
```

### PHP
```php
$ch = curl_init('http://netsuite.test/api/netsuite/ai/ask');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'prompt' => 'What are my recent sales orders?'
]));
$response = curl_exec($ch);
$data = json_decode($response, true);
echo $data['ai_response'];
```

### JavaScript
```javascript
const response = await fetch('/api/netsuite/ai/ask', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    prompt: 'Show me the top vendors'
  })
});
const data = await response.json();
console.log(data.ai_response);
console.log('Generated Query:', data.generated_query);
```

## Example Questions

- "Show me the top 10 customers"
- "What are the accounting periods?"
- "Find customers with email addresses"
- "List all vendors"
- "Show me sales orders from this month"
- "What items do we have in inventory?"
- "Find transactions over $10,000"

## Files Created

```
app/
  Contracts/
    AIModelInterface.php          # Interface for all AI models
  Traits/
    HandlesAIErrors.php           # Common error handling
  Services/
    AI/
      LocalAIModel.php            # Local AI implementation
    NetSuiteAIService.php         # Main orchestration service
  Http/
    Controllers/
      NetSuiteAIController.php    # API endpoints
config/
  ai.php                          # AI configuration
routes/
  api.php                         # Updated with AI routes
```

## Benefits of This Architecture

1. **Flexibility** - Easy to switch between AI models
2. **Extensibility** - Add new models without changing existing code
3. **Testability** - Each component can be tested independently
4. **Type Safety** - Interface ensures all models have required methods
5. **Error Handling** - Centralized error handling with trait
6. **Configuration** - Easy to configure via .env
7. **Logging** - Built-in logging for debugging

## Troubleshooting

### "AI Model not available"
- Check if Ollama/LM Studio is running
- Verify LOCAL_AI_URL is correct
- Test: `curl http://localhost:11434/api/tags`

### "No tables found"
- Check NetSuite permissions
- Verify ScriptCustomRecordType query works
- Fallback to built-in tables is automatic

### "Query generation failed"
- Check AI model is running
- Try a simpler prompt
- Check logs: `storage/logs/laravel.log`

### "Invalid query syntax"
- AI might need better examples
- Use direct query endpoint for testing
- Report issue for prompt improvement

## Performance

- **Local AI**: 2-10 seconds per question (depends on hardware)
- **OpenAI**: 1-3 seconds per question
- **Table caching**: Consider caching table list for 1 hour

## Security

- ⚠️ Add authentication middleware in production
- Rate limit API calls
- Validate and sanitize AI-generated queries
- Log all queries for audit
- Consider read-only database user

## Future Enhancements

- [ ] Cache table names (refresh hourly)
- [ ] Support for OpenAI models
- [ ] Support for Anthropic Claude
- [ ] Conversation history
- [ ] Query optimization hints
- [ ] Multi-turn conversations
- [ ] Query result caching
- [ ] Streaming responses

---

**Status**: ✅ Fully implemented with local AI model support!

