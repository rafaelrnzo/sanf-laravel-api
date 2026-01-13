FROM alpine:3.20

# Install curl and set timezone to Asia/Jakarta
RUN apk add --no-cache curl tzdata && \
  ln -s /usr/share/zoneinfo/Asia/Jakarta /etc/localtime

# Copy crontab files
COPY deployments/development/app/cron/crontab /etc/crontabs/root

# Copy app commands to that will be executed by cron
COPY deployments/development/app/cron/app-cron.sh /usr/local/bin/app-cron
RUN chmod +x /usr/local/bin/app-cron

# Start crond on foreground
CMD ["/usr/sbin/crond", "-f", "-l", "2", "-c", "/etc/crontabs"]
