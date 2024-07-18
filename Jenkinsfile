pipeline {
    agent any

    environment {
        SONAR_SCANNER_HOME = 'C:\\sonar-scanner-6.1.0.4477-windows-x64\\bin'
    }

    stages {
        stage('Load .env file') {
            steps {
                script {
                    // Charger les variables du fichier .env
                    loadEnv()
                }
            }
        }

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
                bat 'vendor\\bin\\phpunit tests\\RedisTest.php'
            }
        }

        stage('Build') {
            steps {
                // Commande pour construire le projet
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

/**
 * Load variables from a .env file.
 * This assumes the .env file is in the same directory as the Jenkinsfile.
 */
def loadEnv() {
    withEnv(readEnv('.env')) {
        echo 'Loaded .env file successfully'
    }
}

/**
 * Read environment variables from a file.
 * @param filename Name of the file to read environment variables from.
 * @return Map of environment variables.
 */
def readEnv(String filename) {
    def envMap = [:]
    new File(filename).eachLine { line ->
        if (line.contains('=')) {
            def (key, value) = line.split('=', 2)
            envMap[key.trim()] = value.trim()
        }
    }
    return envMap
}
