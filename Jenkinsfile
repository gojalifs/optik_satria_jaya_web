pipeline {
    agent any

    parameters {
        choice(name: 'TARGET-COLOR', choices: ['blue', 'green'], description: 'Which one you want to deploy?')
    }

    environment {
        REGISTRY = credentials('docker-registry-url')
        REG_USER = credentials('docker-registry-username')
        REG_PASS = credentials('docker-registry-password')
        APP_KEY  = credentials('laravel-app-key')             // base64:… dari php artisan key:generate --show
        DB_HOST  = credentials('db-host')
        DB_DATABASE = credentials('db-name')
        DB_USERNAME = credentials('db-user')
        DB_PASSWORD = credentials('db-pass')
        TRAEFIK_CERTRESOLVER = 'letsencrypt'
        BUILD_TAG = "${env.BUILD_NUMBER}-${params.TARGET_COLOR}"
    }

    stages {
        stage('checkout') {
            steps { checkout scm }
        }

        stage('login registry') {
            steps {
                sh '''
                    echo "$REG_PASS" | docker login "$REGISTRY" -u "$REG_USER" --password-stdin
                '''
            }
        }

        stage('build image') {
            steps {
                sh '''
                    export APP_ENV=production
                    export APP_KEY="$APP_KEY"
                    export DB_HOST="$DB_HOST"
                    export DB_DATABASE="$DB_DATABASE"
                    export DB_USERNAME="$DB_USERNAME"
                    export DB_PASSWORD="$DB_PASSWORD"
                    export TRAEFIK_CERTRESOLVER="$TRAEFIK_CERTRESOLVER"
                    export REGISTRY="$REGISTRY"
                    export BUILD_TAG="$BUILD_TAG"
                '''

                if [ "$TARGET_COLOR" = "blue" ]; then
                    export BLUE_ACTIVE=true
                    export GREEN_ACTIVE=false
                    docker compose -f docker-compose.blue.yml pull || true
                    docker compose -f docker-compose.blue.yml up -d
                else
                    export BLUE_ACTIVE=false
                    export GREEN_ACTIVE=true
                    docker compose -f docker-compose.green.yml pull || true
                    docker compose -f docker-compose.green.yml up -d
                fi
            }
        }

        stage('Migrate DB (once)'){
            steps {
                // jalankan migrate di container aktif
                sh '''
                    ACTIVE_WEB=$( [ "$TARGET_COLOR" = "blue" ] && echo "laravel-blue-web" || echo "laravel-green-web" )
                    docker exec -e APP_ENV=production $ACTIVE_WEB php artisan migrate --force
                    docker exec $ACTIVE_WEB php artisan optimize
                '''
            }
        }

        stage('Health Check') {
            steps {
                sh '''
                # tes endpoint /health melalui network internal
                    ACTIVE_WEB=$( [ "$TARGET_COLOR" = "blue" ] && echo "sjo-laravel-blue-web" || echo "sjo-laravel-green-web" )
                    for i in $(seq 1 20); do
                    if docker exec $ACTIVE_WEB wget -qO- http://127.0.0.1/health | grep -qi ok; then
                        echo "Healthy"
                        exit 0
                    fi
                    echo "Waiting health..."
                    sleep 3
                    done
                    echo "Healthcheck failed"; exit 1
                '''
            }
        }

        stage('Deactivate Old Color') {
            steps {
                sh '''
                    if [ "$TARGET_COLOR" = "blue" ]; then
                    # matikan label di green (traefik.enable=false) dengan recreate
                    export GREEN_ACTIVE=false; docker compose -f docker-compose.green.yml up -d
                    else
                    export BLUE_ACTIVE=false; docker compose -f docker-compose.blue.yml up -d
                    fi
                '''
            }
        }
    }

    post {
        always {
        sh 'docker logout $REGISTRY || true'
        }
    }
}