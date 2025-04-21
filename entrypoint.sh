#!/bin/bash

CERT_DIR="/etc/apache2/ssl"
KEY="$CERT_DIR/key.pem"
CERT="$CERT_DIR/fullchain.pem"

if [[ ! -f "$KEY" || ! -f "$CERT" ]]; then
  echo "🔒 Generating self-signed certificate..."
  openssl req -x509 -nodes -days 365 \
    -subj "/C=US/ST=Secure/L=Server/O=NATI/CN=localhost" \
    -newkey rsa:2048 -keyout "$KEY" -out "$CERT"
fi

# Start Apache in foreground
exec apache2-foreground
