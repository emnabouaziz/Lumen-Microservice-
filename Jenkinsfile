pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                // Checkout du code depuis GitLab
                git branch: 'develop', url: 'https://gitlab.u-cloudsolutions.xyz/summary-internship/2024/emna-bouaziz/microservice.git'
                echo 'Git Checkout Completed'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                // Analyse SonarQube
                withSonarQubeEnv('SonarQube Server') {
                    sh """
                    sonar-scanner \
                    -Dsonar.projectKey=test-lumen \
                    -Dsonar.projectName='test-lumen' \
                    -Dsonar.sources=. \
                    -Dsonar.host.url=http://localhost:9000 \
                    -Dsonar.php.exclusions=vendor/** \
                    -Dsonar.php.tests.reportPath=test-reports.xml
                    """
                }
                echo 'SonarQube Analysis Completed'
            }
        }
    }

    post {
        always {
            // Archivage des rapports et des artefacts
            junit '**/reports/**/*.xml'
            archiveArtifacts artifacts: 'storage/logs/*.log', allowEmptyArchive: true
        }
    }
}
