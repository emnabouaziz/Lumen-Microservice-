pipeline {
    agent any

    environment {
        SONAR_SCANNER_HOME = 'C:\\sonar-scanner-6.1.0.4477-windows-x64\\bin'
        NEXUS_CREDENTIALS_ID = 'nexus-credentials'  // L'ID des informations d'identification Nexus
        NEXUS_URL = 'http://localhost:8082'
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

        stage('Install packages') {
            steps {
                bat 'composer install'
            }
        }

        stage('Build') {
            steps {
                echo 'Build stage - Lumen does not require a build step'
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

        stage('Package Artifact') {
            steps {
                script {
                    def directoryToZip = 'C:\\Users\\DELL\\Documents\\boilerplateeeee\\microservice'  // Remplacez par le chemin de votre répertoire
                    def zipFilePath = 'artifact.zip'
                    
                    // Créer un fichier ZIP de l'application
                    bat "powershell Compress-Archive -Path ${directoryToZip}\\* -DestinationPath ${zipFilePath}"
                    echo 'Artifact packaged'
                }
            }
        }

        stage('Upload to Nexus') {
            steps {
                script {
                    // Trouver l'artifact à uploader
                    def artifactPath = findFiles(glob: 'artifact.zip')[0].path

                    // Utiliser le commit ID comme version pour l'upload
                    def version = env.GIT_COMMIT_ID

                    // Upload l'artifact sur Nexus
                    nexusArtifactUploader(
                        nexusVersion: 'nexus3',
                        protocol: 'http',
                        nexusUrl: env.NEXUS_URL,
                        groupId: '',
                        version: version,
                        repository: 'maven-releases',
                        credentialsId: env.NEXUS_CREDENTIALS_ID,
                        artifacts: [
                            [artifactId: 'my-app', classifier: '', file: artifactPath, type: 'zip']  
                        ]
                    )
                    echo 'Artifact uploaded to Nexus with commit ID as version'
                }
            }
        }
    }
}
