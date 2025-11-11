# Postman Request Examples for NetSuite AI Agent

## 1. Ask a Question (Main AI Endpoint)

### Request
**Method:** `POST`  
**URL:** `http://netsuite.test/api/netsuite/ai/ask`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**
```json
{
  "prompt": "Show me the top 5 customers"
}
```

### Alternative Body Options
```json
{
  "question": "What are the accounting periods?"
}
```

or

```json
{
  "prompt": "Find all vendors with email addresses"
}
```

---

## 2. Check AI Status

### Request
**Method:** `GET`  
**URL:** `http://netsuite.test/api/netsuite/ai/status`

**Headers:**
```
Accept: application/json
```

**No Body Required**

### Expected Response
```json
{
  "available": true,
  "model": "Local AI (mistral)",
  "driver": "local"
}
```

---

## 3. List Available Tables

### Request
**Method:** `GET`  
**URL:** `http://netsuite.test/api/netsuite/ai/tables`

**Headers:**
```
Accept: application/json
```

**No Body Required**

### Expected Response
```json
{
  "success": true,
  "count": 501,
  "tables": [
    "Account",
    "AccountType",
    "AccountingBook",
    "AccountingContext",
    "AccountingPeriod",
    "customer",
    "vendor",
    "transaction",
    "..."
  ]
}
```

---

## 4. Get Table Schema

### Request
**Method:** `GET`  
**URL:** `http://netsuite.test/api/netsuite/ai/schema?table=customer`

**Headers:**
```
Accept: application/json
```

**Query Parameters:**
- `table`: `customer` (or any table name)

**No Body Required**

### Expected Response
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
    {
      "columnname": "companyname",
      "datatype": "VARCHAR",
      "nullable": "Y"
    },
    {
      "columnname": "email",
      "datatype": "VARCHAR",
      "nullable": "Y"
    }
  ]
}
```

---

## 5. Direct SuiteQL Query (No AI)

### Request
**Method:** `POST`  
**URL:** `http://netsuite.test/api/netsuite/suiteql`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**
```json
{
  "query": "SELECT id, companyname, email FROM customer FETCH FIRST 10 ROWS ONLY"
}
```

---

## Example Questions to Try

### Simple Questions
```json
{"prompt": "Show me all customers"}
{"prompt": "List the accounting periods"}
{"prompt": "What vendors do we have?"}
{"prompt": "Show me 10 transactions"}
```

### More Complex Questions
```json
{"prompt": "Find customers with email addresses"}
{"prompt": "Show me the top 20 customers by name"}
{"prompt": "What are the recent sales orders?"}
{"prompt": "List all items in inventory"}
```

### Specific Queries
```json
{"prompt": "Find accounting periods from 2025"}
{"prompt": "Show me customers that have ABC in their name"}
{"prompt": "What are the account types?"}
{"prompt": "List all departments"}
```

---

## Expected AI Response Format

### Successful Response
```json
{
  "success": true,
  "question": "Show me the top 5 customers",
  "generated_query": "SELECT id, companyname, email FROM customer FETCH FIRST 5 ROWS ONLY",
  "raw_data": {
    "links": [...],
    "count": 5,
    "hasMore": false,
    "items": [
      {
        "id": "1264",
        "companyname": "1 Renault",
        "email": "contact@renault.com"
      },
      {
        "id": "325",
        "companyname": "ABC Marketing Inc",
        "email": "info@abcmarketing.com"
      },
      ...
    ],
    "offset": 0,
    "totalResults": 5
  },
  "ai_response": "Here are the top 5 customers:\n\n1. 1 Renault (ID: 1264) - contact@renault.com\n2. ABC Marketing Inc (ID: 325) - info@abcmarketing.com\n3. ...",
  "ai_model": "Local AI (mistral)",
  "tables_available": 501
}
```

### Error Response
```json
{
  "success": false,
  "error": "Error message here",
  "ai_model": "Local AI (mistral)"
}
```

---

## Postman Collection Import (JSON)

You can import this into Postman:

```json
{
  "info": {
    "name": "NetSuite AI Agent API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Ask Question",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          },
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"prompt\": \"Show me the top 5 customers\"\n}"
        },
        "url": {
          "raw": "http://netsuite.test/api/netsuite/ai/ask",
          "protocol": "http",
          "host": ["netsuite", "test"],
          "path": ["api", "netsuite", "ai", "ask"]
        }
      }
    },
    {
      "name": "Check AI Status",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "url": {
          "raw": "http://netsuite.test/api/netsuite/ai/status",
          "protocol": "http",
          "host": ["netsuite", "test"],
          "path": ["api", "netsuite", "ai", "status"]
        }
      }
    },
    {
      "name": "List Tables",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "url": {
          "raw": "http://netsuite.test/api/netsuite/ai/tables",
          "protocol": "http",
          "host": ["netsuite", "test"],
          "path": ["api", "netsuite", "ai", "tables"]
        }
      }
    },
    {
      "name": "Get Table Schema",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "url": {
          "raw": "http://netsuite.test/api/netsuite/ai/schema?table=customer",
          "protocol": "http",
          "host": ["netsuite", "test"],
          "path": ["api", "netsuite", "ai", "schema"],
          "query": [
            {
              "key": "table",
              "value": "customer"
            }
          ]
        }
      }
    },
    {
      "name": "Direct SuiteQL Query",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          },
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"query\": \"SELECT id, companyname, email FROM customer FETCH FIRST 10 ROWS ONLY\"\n}"
        },
        "url": {
          "raw": "http://netsuite.test/api/netsuite/suiteql",
          "protocol": "http",
          "host": ["netsuite", "test"],
          "path": ["api", "netsuite", "suiteql"]
        }
      }
    }
  ]
}
```

---

## Tips for Using in Postman

1. **Save as Collection**: Import the JSON above or manually create requests
2. **Environment Variables**: Create variables for `base_url` = `http://netsuite.test`
3. **Save Responses**: Save example responses for reference
4. **Test Scripts**: Add tests to validate responses
5. **Timeout**: Set timeout to 120 seconds for AI requests (they can take longer)

---

## Quick Test in Postman

1. Open Postman
2. Create a new **POST** request
3. URL: `http://netsuite.test/api/netsuite/ai/ask`
4. Headers → Add `Content-Type: application/json`
5. Body → Select **raw** and **JSON**
6. Paste:
   ```json
   {
     "prompt": "Show me 5 customers"
   }
   ```
7. Click **Send**
8. Wait 5-15 seconds for AI to process
9. View the response!

---

## Troubleshooting

### "Connection refused"
- Make sure your Laravel server is running
- Check the URL matches your local domain

### "AI model not available"
- Verify Ollama is running: `curl http://localhost:11434/api/tags`
- Check `.env` has correct `LOCAL_AI_MODEL` setting

### "Timeout"
- AI responses can take 10-30 seconds
- Increase timeout in Postman: Settings → Request timeout

### "Query parameter is required"
- Use `prompt` or `question` in your JSON body
- Check Content-Type header is set

---

**Status**: ✅ Ready to test in Postman!

