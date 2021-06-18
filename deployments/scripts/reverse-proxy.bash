#!/usr/bin/env bash

# SITE=api--sanf.nbs.co.id
# TARGET_PORT=4005

rm /etc/nginx/sites-enabled/default

cat > /etc/nginx/sites-available/${SITE}.conf <<EOF

server{
	server_name ${SITE};
	proxy_set_header Host \$host;
	proxy_set_header X-Real-IP \$remote_addr;
	proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
	proxy_set_header X-Forwarded-Proto \$scheme;
    client_header_timeout       600;

	location / {
        proxy_connect_timeout       600;
        proxy_send_timeout          600;
        proxy_read_timeout          600;
        send_timeout                600;
        client_max_body_size 100M;
        proxy_pass http://127.0.0.1:${TARGET_PORT};
	}
}

EOF

ln -s /etc/nginx/sites-available/${SITE}.conf /etc/nginx/sites-enabled

service nginx restart

certbot --nginx  -d ${SITE} --register-unsafely-without-email --non-interactive --agree-tos
