pipeline {
  agent any
  stages {
    stage('Build') {
      agent {
        docker {
          image 'docker pull php:7.4.5-cli-buster'
        }

      }
      steps {
        sh 'curl -sS https://getcomposer.org/installer | php'
        sh 'php composer.phar install -o -a -n --no-scripts --no-suggest --prefer-dist'
        sh './vendor/bin/phpunit tests'
      }
    }

  }
}