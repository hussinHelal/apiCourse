#!/bin/bash

# API Testing Script
# Usage: ./api-test.sh METHOD ROUTE [DATA] [OPTIONS]
# Example: ./api-test.sh POST /api/categories '{"name":"Test","description":"Desc"}'

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

# Default values
BASE_URL="${API_BASE_URL:-http://localhost:8080}"
CONTENT_TYPE="application/json"
ACCEPT="application/json"

# Function to display usage
usage() {
    echo -e "${BLUE}Usage:${NC}"
    echo "  $0 METHOD ROUTE [DATA] [OPTIONS]"
    echo ""
    echo -e "${BLUE}Examples:${NC}"
    echo "  $0 GET /api/categories"
    echo "  $0 POST /api/categories '{\"name\":\"Test\",\"description\":\"Desc\"}'"
    echo "  $0 PUT /api/categories/1 '{\"name\":\"Updated\"}'"
    echo "  $0 DELETE /api/categories/1"
    echo "  $0 GET /api/user/2 --headers"
    echo ""
    echo -e "${BLUE}Options:${NC}"
    echo "  -h, --help              Show this help message"
    echo "  -b, --base-url URL      Set base URL (default: http://localhost:8080)"
    echo "  -t, --token TOKEN       Add Bearer token for authentication"
    echo "  -c, --content-type TYPE Set Content-Type header (default: application/json)"
    echo "  -a, --accept TYPE       Set Accept header (default: application/json)"
    echo "  -v, --verbose           Show request details"
    echo "  -s, --save FILE         Save response to file"
    echo "  --headers               Show response headers"
    echo "  --headers-only          Show only response headers (no body)"
    echo ""
    echo -e "${BLUE}Environment Variables:${NC}"
    echo "  API_BASE_URL            Default base URL"
    echo "  API_TOKEN               Default bearer token"
    exit 0
}

# Parse arguments
if [ $# -lt 2 ]; then
    usage
fi

METHOD=$1
ROUTE=$2
DATA=""
TOKEN="${API_TOKEN:-}"
VERBOSE=false
SAVE_FILE=""
SHOW_HEADERS=false
HEADERS_ONLY=false

shift 2

# Check if third argument is data (not starting with -)
if [ $# -gt 0 ] && [[ ! "$1" =~ ^- ]]; then
    DATA="$1"
    shift
fi

# Parse options
while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            usage
            ;;
        -b|--base-url)
            BASE_URL="$2"
            shift 2
            ;;
        -t|--token)
            TOKEN="$2"
            shift 2
            ;;
        -c|--content-type)
            CONTENT_TYPE="$2"
            shift 2
            ;;
        -a|--accept)
            ACCEPT="$2"
            shift 2
            ;;
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        -s|--save)
            SAVE_FILE="$2"
            shift 2
            ;;
        --headers)
            SHOW_HEADERS=true
            shift
            ;;
        --headers-only)
            SHOW_HEADERS=true
            HEADERS_ONLY=true
            shift
            ;;
        *)
            echo -e "${RED}Error: Unknown option $1${NC}"
            usage
            ;;
    esac
done

# Build URL
URL="${BASE_URL}${ROUTE}"

# Show request details if verbose
if [ "$VERBOSE" = true ]; then
    echo -e "${YELLOW}=== REQUEST DETAILS ===${NC}"
    echo -e "${BLUE}Method:${NC} $METHOD"
    echo -e "${BLUE}URL:${NC} $URL"
    echo -e "${BLUE}Content-Type:${NC} $CONTENT_TYPE"
    echo -e "${BLUE}Accept:${NC} $ACCEPT"
    if [ -n "$TOKEN" ]; then
        echo -e "${BLUE}Authorization:${NC} Bearer ***${TOKEN: -8}"
    fi
    if [ -n "$DATA" ]; then
        echo -e "${BLUE}Data:${NC} $DATA"
    fi
    echo ""
fi

# Create temp files for headers and body
HEADER_FILE=$(mktemp)
BODY_FILE=$(mktemp)

# Cleanup temp files on exit
trap "rm -f $HEADER_FILE $BODY_FILE" EXIT

# Build curl command
CURL_CMD="curl -X $METHOD"
CURL_CMD="$CURL_CMD -H 'Content-Type: $CONTENT_TYPE'"
CURL_CMD="$CURL_CMD -H 'Accept: $ACCEPT'"

if [ -n "$TOKEN" ]; then
    CURL_CMD="$CURL_CMD -H 'Authorization: Bearer $TOKEN'"
fi

if [ -n "$DATA" ]; then
    CURL_CMD="$CURL_CMD -d '$DATA'"
fi

# Add options to capture headers and body separately
CURL_CMD="$CURL_CMD -D '$HEADER_FILE' -o '$BODY_FILE' -s -w '\nHTTP_CODE:%{http_code}'"
CURL_CMD="$CURL_CMD '$URL'"

# Execute request
eval $CURL_CMD > /dev/null

# Extract HTTP status code from headers
HTTP_CODE=$(head -n 1 "$HEADER_FILE" | awk '{print $2}')

# Show response headers if requested
if [ "$SHOW_HEADERS" = true ]; then
    echo -e "${CYAN}=== RESPONSE HEADERS ===${NC}"
    
    # Skip the first line (HTTP status) and show the rest
    tail -n +2 "$HEADER_FILE" | while IFS= read -r line; do
        # Stop at empty line (end of headers)
        if [ -z "$line" ]; then
            break
        fi
        
        # Color code the header name
        HEADER_NAME=$(echo "$line" | cut -d: -f1)
        HEADER_VALUE=$(echo "$line" | cut -d: -f2-)
        
        # Highlight custom headers (X-*)
        if [[ "$HEADER_NAME" =~ ^X- ]]; then
            echo -e "${MAGENTA}${HEADER_NAME}:${NC}${GREEN}${HEADER_VALUE}${NC}"
        else
            echo -e "${BLUE}${HEADER_NAME}:${NC}${HEADER_VALUE}"
        fi
    done
    echo ""
fi

# Show response body unless headers-only is set
if [ "$HEADERS_ONLY" = false ]; then
    echo -e "${YELLOW}=== RESPONSE BODY ===${NC}"
    
    # Pretty print JSON if jq is available
    if command -v jq &> /dev/null && [[ "$CONTENT_TYPE" == *"json"* ]]; then
        cat "$BODY_FILE" | jq -C
    else
        cat "$BODY_FILE"
    fi
    
    echo ""
fi

# Show HTTP status
if [ "$HTTP_CODE" -ge 200 ] && [ "$HTTP_CODE" -lt 300 ]; then
    echo -e "${GREEN}HTTP Status: $HTTP_CODE${NC}"
elif [ "$HTTP_CODE" -ge 400 ]; then
    echo -e "${RED}HTTP Status: $HTTP_CODE${NC}"
else
    echo -e "${YELLOW}HTTP Status: $HTTP_CODE${NC}"
fi

# Save to file if requested
if [ -n "$SAVE_FILE" ]; then
    cat "$BODY_FILE" > "$SAVE_FILE"
    echo -e "${GREEN}Response saved to: $SAVE_FILE${NC}"
fi

# Check response status
if [ "$HTTP_CODE" -ge 400 ]; then
    echo -e "${RED}⚠  Request failed${NC}"
    exit 1
else
    echo -e "${GREEN}✓ Request completed${NC}"
fi
