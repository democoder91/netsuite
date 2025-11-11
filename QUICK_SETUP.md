# Quick Setup Guide - NetSuite AI Agent

## ✅ Implementation Complete!

The AI agent has been completely reimplemented with a flexible, multi-model architecture.

## What Was Built

### 1. **Interface-Based Architecture**
- `AIModelInterface` - Contract for all AI models
- `HandlesAIErrors` trait - Common error handling
- Easy to add new AI models (OpenAI, Claude, etc.)

### 2. **Local AI Model**
- First implementation using local AI (Ollama/LM Studio)
- No external API costs
- Full privacy - data stays local

### 3. **Smart Query Flow**
```
User Prompt
    ↓
Fetch NetSuite Tables (ScriptCustomRecordType)
    ↓
Format Table Names (Accounting Period → AccountingPeriod)
    ↓
AI Generates Query (Prompt + Tables → SQL)
    ↓
Execute on NetSuite
    ↓
AI Formats Response (Data → Human Text)
    ↓
Return to User
```

## Quick Start

### Step 1: Install Ollama (Local AI)

**Windows:**
```bash
# Download from https://ollama.ai
# Run installer
```

**macOS:**
```bash
brew install ollama
```

**Linux:**
```bash
curl https://ollama.ai/install.sh | sh
```

### Step 2: Pull a Model

```bash
# Recommended: Llama 2
ollama pull llama2

# OR: Mistral (faster, good quality)
ollama pull mistral

# OR: CodeLlama (best for SQL)
ollama pull codellama
```

### Step 3: Configure Laravel

Add to `.env`:
```env
AI_MODEL=local
LOCAL_AI_URL=http://localhost:11434
LOCAL_AI_MODEL=llama2
```

### Step 4: Test

```bash
# Check AI status
curl http://netsuite.test/api/netsuite/ai/status

# List available tables
curl http://netsuite.test/api/netsuite/ai/tables

# Ask a question
curl -X POST http://netsuite.test/api/netsuite/ai/ask \
  -H "Content-Type: application/json" \
  -d '{"prompt":"Show me 5 customers"}'
```

Or run the test script:
```bash
php test_ai_architecture.php
```

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/netsuite/ai/ask` | POST | Ask natural language questions |
| `/api/netsuite/ai/status` | GET | Check if AI is available |
| `/api/netsuite/ai/tables` | GET | List all queryable tables |
| `/api/netsuite/ai/schema?table=X` | GET | Get table schema |

## Example Usage

### Ask a Question
```bash
curl -X POST http://netsuite.test/api/netsuite/ai/ask \
  -H "Content-Type: application/json" \
  -d '{
    "prompt": "Show me the accounting periods"
  }'
```

**Response:**
```json
{
  "success": true,
  "question": "Show me the accounting periods",
  "generated_query": "SELECT id, name FROM AccountingPeriod FETCH FIRST 20 ROWS ONLY",
  "raw_data": {...},
  "ai_response": "Here are the accounting periods: 1. January 2025, 2. February 2025...",
  "ai_model": "Local AI (llama2)",
  "tables_available": 45
}
```

## Files Created

```
app/
├── Contracts/
│   └── AIModelInterface.php         # AI model contract
├── Traits/
│   └── HandlesAIErrors.php          # Error handling
├── Services/
│   ├── AI/
│   │   └── LocalAIModel.php         # Local AI implementation
│   └── NetSuiteAIService.php        # Main service
└── Http/Controllers/
    └── NetSuiteAIController.php     # API controller

config/
└── ai.php                            # AI configuration

routes/
└── api.php                           # API routes

Documentation/
├── AI_ARCHITECTURE.md                # Full documentation
└── QUICK_SETUP.md                    # This file
```

## Adding More AI Models

### OpenAI (Future)

1. Create `app/Services/AI/OpenAIModel.php`
2. Implement `AIModelInterface`
3. Add to `config/ai.php`
4. Update service factory

### Anthropic Claude (Future)

Same process as OpenAI

## Configuration

### .env
```env
# AI Model Selection
AI_MODEL=local                        # Options: local, openai, anthropic

# Local AI (Ollama/LM Studio)
LOCAL_AI_URL=http://localhost:11434
LOCAL_AI_MODEL=llama2
LOCAL_AI_TIMEOUT=60

# OpenAI (when implemented)
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4

# Anthropic (when implemented)
ANTHROPIC_API_KEY=sk-ant-...
ANTHROPIC_MODEL=claude-3-sonnet-20240229
```

## Troubleshooting

### "AI model not available"

**Check Ollama is running:**
```bash
curl http://localhost:11434/api/tags
```

**Should return:**
```json
{"models":[{"name":"llama2:latest",...}]}
```

**Start Ollama:**
```bash
ollama serve
```

### "No tables found"

The system falls back to basic tables automatically:
- customer
- vendor
- transaction
- item
- employee
- account

### "Query generation failed"

- Simplify your question
- Check AI model is running
- Try a different model
- Check logs: `storage/logs/laravel.log`

## Testing

```bash
# Run test script
php test_ai_architecture.php

# Manual tests
curl http://netsuite.test/api/netsuite/ai/status
curl http://netsuite.test/api/netsuite/ai/tables
curl -X POST http://netsuite.test/api/netsuite/ai/ask \
  -H "Content-Type: application/json" \
  -d '{"prompt":"Show me 3 customers"}'
```

## Example Questions

Try asking:
- "Show me the top 10 customers"
- "What are the accounting periods?"
- "List all vendors"
- "Find customers with email addresses"
- "Show me recent transactions"
- "What items do we have?"

## Performance

- **Local AI (Llama2)**: 5-15 seconds per question
- **Local AI (Mistral)**: 3-10 seconds per question
- **OpenAI (future)**: 1-3 seconds per question

## Next Steps

1. ✅ Test with local AI
2. ✅ Try different prompts
3. ⏳ Implement OpenAI model (optional)
4. ⏳ Implement Claude model (optional)
5. ⏳ Add caching for table names
6. ⏳ Add conversation history

## Support

Full documentation: `AI_ARCHITECTURE.md`

---

**Status**: ✅ Ready to use with local AI!
**Cost**: $0 (completely free with local AI)
**Privacy**: ✅ All data stays on your server

