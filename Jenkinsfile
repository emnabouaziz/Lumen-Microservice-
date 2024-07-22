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
                    def scmInfo = checkout scm: [
                        $class: 'GitSCM',
                        branches: [[name: '*/develop']],
                        userRemoteConfigs: [[url: 'https://gitlab.u-cloudsolutions.xyz/summary-internship/2024/emna-bouaziz/microservice.git']]
                    ]
                    env.GIT_COMMIT_ID = scmInfo.GIT_COMMIT.take(8) 
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

        stage('Package Artifact') {
            steps {
                script {
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
                    def artifactPath = "${env.WORKSPACE}\\artifact.zip"
                    def shortVersion = env.GIT_COMMIT_ID  // Use shortened commit ID as version
                    def nexusVersion = "v${shortVersion}" // Format version tag as 'v{shortVersion}'

                    withCredentials([usernamePassword(credentialsId: 'nexus-credentials', passwordVariable: 'NEXUS_PASSWORD', usernameVariable: 'NEXUS_USERNAME')]) {
                        def nexusUrl = "${env.NEXUS_URL}/repository/maven-releases/${env.MAVEN_GROUP_ID.replace('.', '/')}/${env.ARTIFACT_ID}/${nexusVersion}/${env.ARTIFACT_ID}-${nexusVersion}.zip"

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
                    def versionTag = params.VERSION_TAG
                    def nexusUrl = "${env.NEXUS_URL}/repository/maven-releases/${env.MAVEN_GROUP_ID.replace('.', '/')}/${env.ARTIFACT_ID}/${versionTag}/${env.ARTIFACT_ID}-${versionTag}.zip"

                    echo "Checking artifact in Nexus at URL: ${nexusUrl}"

                    // Execute curl command to get HTTP response code
                    def response = bat(script: """
                        curl -o NUL -s -w %%{http_code} "${nexusUrl}"
                        """, returnStdout: true).trim()

                    echo "HTTP response code: ${response}"
                }
            }
        }

        stage('Download Artifact') {
            steps {
                script {
                    def versionTag = params.VERSION_TAG
                    def nexusUrl = "${env.NEXUS_URL}/repository/maven-releases/${env.MAVEN_GROUP_ID.replace('.', '/')}/${env.ARTIFACT_ID}/${versionTag}/${env.ARTIFACT_ID}-${versionTag}.zip"

                    bat "curl -o ${DOWNLOAD_PATH} \"${nexusUrl}\""
                    echo "Artifact downloaded to ${DOWNLOAD_PATH}"
                }
            }
        }

        stage('Unzip Artifact') {
            steps {
                script {
                    def zipFilePath = DOWNLOAD_PATH
                    def extractPath = EXTRACT_PATH

                    bat "if not exist ${extractPath} mkdir ${extractPath}"
                    bat "powershell Expand-Archive -Path ${zipFilePath} -DestinationPath ${extractPath} -Force"
                    echo "Artifact unzipped to ${extractPath}"
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    def version = env.GIT_COMMIT_ID
                    def dockerImageName = "my-app:${version}"

                    echo "Building Docker image: ${dockerImageName}"

                    bat """
                    docker build -t ${dockerImageName} .
                    """
                    echo "Docker image built: ${dockerImageName}"
                }
            }
        }

        stage('Tag and Push Docker Image to Nexus') {
            steps {
                script {
                    def version = env.GIT_COMMIT_ID
                    def dockerImageName = "my-app:${version}"
                    def nexusRepoUrl = "http://localhost:8082/repository/docker-host/"

                    echo "Tagging Docker image: ${dockerImageName}"

                    bat """
                    docker tag ${dockerImageName} ${nexusRepoUrl}${dockerImageName}
                    """

                    echo "Pushing Docker image: ${dockerImageName} to Nexus"

                    bat """
                    docker push ${nexusRepoUrl}${dockerImageName}
                    """
                    echo "Docker image pushed to Nexus"
                }
            }
        }

        stage('Deploy Container') {
            steps {
                script {
                    def version = env.GIT_COMMIT_ID
                    def dockerImageName = "my-app:${version}"

                    echo "Deploying Docker container with image: ${dockerImageName}"

                    bat """
                    docker run -d --name my-app-container ${dockerImageName}
                    """
                    echo "Docker container deployed"
                }
            }
        }
    }
}
