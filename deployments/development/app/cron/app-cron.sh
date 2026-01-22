#!/bin/sh

# Get job name
JOB_NAME=${1}

if [[ -z $JOB_NAME ]]; then
  echo "  > app-cron: [ERROR] JOB cannot be empty"
  exit 1;
fi

echo "  > app-cron: [INFO] Running job ${JOB_NAME}. Timestamp=$(date)"

# Determine Basic Auth token from id & secret
if [[ -z $CRON_CLIENT_ID || -z $CRON_CLIENT_SECRET ]]; then
  echo "  > app-cron: [ERROR] CRON_CLIENT_ID & CRON_CLIENT_SECRET must be provided"
  exit 1;
fi

CRON_BASIC_AUTH=$(printf '%s:%s' "$CRON_CLIENT_ID" "$CRON_CLIENT_SECRET" | base64 | tr -d '\n')

# Execute job
curl --location "${CRON_BASE_URL}/cron/${JOB_NAME}" \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header "Authorization: Basic ${CRON_BASIC_AUTH}" \
--data '{
    "payload": "{}"
}'

echo ""
echo "  > app-cron: [INFO] Done"
