pipeline {
    agent any

    environment {
        SONAR_SCANNER_HOME = 'C:\\sonar-scanner-6.1.0.4477-windows-x64\\bin'
        NEXUS_CREDENTIALS_ID = 'nexus-credentials'
        NEXUS_URL = 'http://localhost:8082'
        MAVEN_GROUP_ID = 'com.mycompany.project'
        ARTIFACT_ID = 'my-app'
        DOWNLOAD_PATH = "${env.WORKSPACE}\\artifact.zip"
        EXTRACT_PATH = "${env.WORKSPACE}\\extracted"
    }

    parameters {
        string(name: 'VERSION_TAG', defaultValue: '', description: 'Version tag to check and deploy')
    }

    stages {
        stage('Git Checkout') {
            steps {
                script {
                    // Récupère le code depuis GitLab
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
                // Installe les dépendances PHP
                bat 'composer install'
            }
        }

        stage('Build') {
            steps {
                // Affiche un message indiquant qu'aucune étape de construction n'est nécessaire pour Lumen
                echo 'Build stage - Lumen does not require a build step'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                // Exécute l'analyse de code avec SonarQube
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
                    // Compresse les fichiers du projet en un fichier ZIP
                    def directoryToZip = 'C:\\Users\\DELL\\Documents\\boilerplateeeee\\microservice'
                    def zipFilePath = "${env.WORKSPACE}\\artifact.zip"

                    bat "powershell Compress-Archive -Path ${directoryToZip}\\* -DestinationPath ${zipFilePath} -Update"
                    echo 'Artifact packaged'
                }
            }
        }

        stage('Upload to Nexus') {
            steps {
                script {
                    // Télécharge l'artifact ZIP dans Nexus
                    def artifactPath = "${env.WORKSPACE}\\artifact.zip"
                    def version = env.GIT_COMMIT_ID

                    withCredentials([usernamePassword(credentialsId: 'nexus-credentials', passwordVariable: 'NEXUS_PASSWORD', usernameVariable: 'NEXUS_USERNAME')]) {
                        def nexusUrl = "${env.NEXUS_URL}/repository/maven-releases/${env.MAVEN_GROUP_ID.replace('.', '/')}/${env.ARTIFACT_ID}/${version}/${env.ARTIFACT_ID}-${version}.zip"

                        bat """
                        curl -v -u ${NEXUS_USERNAME}:${NEXUS_PASSWORD} --upload-file ${artifactPath} ${nexusUrl}
                        """
                    }
                    echo 'Artifact uploaded to Nexus with commit ID as version'
                }
            }
        }

        stage('Check Artifact in Nexus') {
            steps {
                script {
                    // Vérifie si l'artifact est présent dans Nexus
                    def versionTag = params.VERSION_TAG
                    def nexusUrl = "${env.NEXUS_URL}/repository/maven-releases/${env.MAVEN_GROUP_ID.replace('.', '/')}/${env.ARTIFACT_ID}/${versionTag}/${env.ARTIFACT_ID}-${versionTag}.zip"

                    def response = bat(script: "curl -o NUL -s -w '%{http_code}' ${nexusUrl}", returnStdout: true).trim()
                    if (response == '200') {
                        echo "Artifact found in Nexus with version tag ${versionTag}"
                    } else {
                        error "Artifact not found or request failed with HTTP status ${response}"
                    }
                }
            }
        }

        stage('Download Artifact') {
            steps {
                script {
                    // Télécharge l'artifact depuis Nexus
                    def versionTag = params.VERSION_TAG
                    def nexusUrl = "${env.NEXUS_URL}/repository/maven-releases/${env.MAVEN_GROUP_ID.replace('.', '/')}/${env.ARTIFACT_ID}/${versionTag}/${env.ARTIFACT_ID}-${versionTag}.zip"

                    bat "wget -O ${DOWNLOAD_PATH} ${nexusUrl}"
                    echo "Artifact downloaded to ${DOWNLOAD_PATH}"
                }
            }
        }

        stage('Unzip Artifact') {
            steps {
                script {
                    // Décompresse l'archive ZIP téléchargée
                    def zipFilePath = DOWNLOAD_PATH
                    def extractPath = EXTRACT_PATH

                    // Crée le répertoire si nécessaire
                    bat "if not exist ${extractPath} mkdir ${extractPath}"

                    // Décompresse le fichier
                    bat "powershell Expand-Archive -Path ${zipFilePath} -DestinationPath ${extractPath} -Force"
                    echo "Artifact unzipped to ${extractPath}"
                }
            }
        }
    }
}
