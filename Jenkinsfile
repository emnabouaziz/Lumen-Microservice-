pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                git branch: 'develop', url: 'https://gitlab.u-cloudsolutions.xyz/summary-internship/2024/emna-bouaziz/microservice.git'
            }
        }

        stage('SonarQube analysis') {
            steps {
                script {
                    docker.image('sonarsource/sonar-scanner-cli:latest').inside {
                        withSonarQubeEnv('SonarQube Server') {
                            sh '/opt/sonar-scanner/bin/sonar-scanner'
                        }
                    }
                }
            }
        }
    }

    post {
        always {
            junit '**/reports/**/*.xml'
            archiveArtifacts artifacts: 'storage/logs/lumen.log', allowEmptyArchive: true
        }
    }
}
