pipeline {
    agent any

    stages {
        stage('Git Checkout') {
            steps {
                // Checkout code from GitLab
                git branch: 'develop', url: 'https://gitlab.u-cloudsolutions.xyz/summary-internship/2024/emna-bouaziz/microservice.git'
                echo 'Git Checkout Completed'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                // Run SonarQube analysis
                withSonarQubeEnv('sonarqube') {
                    bat '''
                    sonar-scanner.bat ^
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

    post {
        always {
            // Archive test reports and artifacts
            junit '**/reports/**/*.xml'
            archiveArtifacts artifacts: 'storage/logs/*.log', allowEmptyArchive: true
        }
    }
}
