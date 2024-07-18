pipeline {
    agent any

    environment {
        SONAR_SCANNER_HOME = 'C:\\sonar-scanner-6.1.0.4477-windows-x64\\bin'
        REDIS_CLIENT=predis
        REDIS_HOST=127.0.0.1
        REDIS_PASSWORD=null
        REDIS_PORT=6379
        REDIS_DATABASE=0
    }

    stages {
        stage('Git Checkout') {
            steps {
                script {
                    def scmInfo = checkout scm: [
                        $class: 'GitSCM', 
                        branches: [[name: '*/develop']], 
                        userRemoteConfigs: [[url: 'https://gitlab.u-cloudsolutions.xyz/summary-internship/2024/emna-bouaziz/microservice.git']]
                    ]
                    env.GIT_COMMIT_ID = scmInfo.GIT_COMMIT
                    echo "Checked out commit ID: ${env.GIT_COMMIT_ID}"
                }
            }
        }

        stage('Install package') {
            steps {
                // Commande pour installer les dépendances
                bat 'composer install'
            }
        }

        stage('Unit tests') {
            steps {
                // Commande pour exécuter les tests unitaires
                bat 'vendor\\bin\\phpunit tests'
            }
        }

        stage('Build') {
            steps {
                
                echo 'Build stage - Lumen does not require a build step'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                // Analyse SonarQube
                bat '''
                "C:\\sonar-scanner-6.1.0.4477-windows-x64\\bin\\sonar-scanner.bat" ^
                -Dsonar.projectKey=test-lumen ^
                -Dsonar.projectName="test-lumen" ^
                -Dsonar.sources=. ^
                -Dsonar.host.url=http://localhost:9000 ^
                -Dsonar.login=sqp_b166d25d821ec2b6bc0efa84baba4f556e622820 ^
                -Dsonar.exclusions=vendor/**
                '''
                echo 'SonarQube Analysis Completed'
            }
        }
    }
}
