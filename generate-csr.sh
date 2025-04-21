#!/bin/bash

mkdir -p /etc/apache2/ssl

openssl req -new -newkey rsa:2048 -nodes \
  -keyout /etc/apache2/ssl/private.key \
  -out /etc/apache2/ssl/request.csr \
  -subj "/C=US/ST=State/L=City/O=Org/CN=example.com"

echo "📄 CSR saved to /etc/apache2/ssl/request.csr"
