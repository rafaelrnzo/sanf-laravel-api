FROM postgres:13.9

# Install necessary packages for building pgsodium
RUN apt-get update && apt-get install -y \
    build-essential \
    libsodium-dev \
    postgresql-server-dev-13 \
    git \
    clang

# Clone and build pgsodium
RUN git clone --depth 1 --branch v3.1.9 https://github.com/michelp/pgsodium.git /usr/src/pgsodium && \
    cd /usr/src/pgsodium && \
    make && \
    make install

# Clean up
RUN apt-get remove --purge -y \
    build-essential \
    libsodium-dev \
    postgresql-server-dev-13 \
    git \
    clang \
    && apt-get clean

# Ensure the postgres user has access to the built extension
RUN chown -R postgres:postgres /var/lib/postgresql

# Run PostgreSQL
CMD ["postgres", "-c", "pgsodium.enable_event_trigger=off"]
