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
