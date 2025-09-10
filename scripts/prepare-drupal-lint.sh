#!/bin/bash

# Common stuff among php drupal linters.

# Check if php is available
php --version

# Check if composer is available
composer --version

# Set target Drupal core version environment variable
if [ -z "$TARGET_DRUPAL_CORE_VERSION" ]; then
  TARGET_DRUPAL_CORE_VERSION=11
fi

echo "TARGET_DRUPAL_CORE_VERSION: $TARGET_DRUPAL_CORE_VERSION"

# Set up Composer home directory
export COMPOSER_HOME=/tmp
cd /tmp

# Create composer.json
cat > composer.json << EOF
{
    "require-dev": {
        "drupal/coder": "*",
        "phpcompatibility/php-compatibility": "*",
        "dealerdirect/phpcodesniffer-composer-installer": "*"
    },
    "config": {
        "allow-plugins": {
            "dealerdirect/phpcodesniffer-composer-installer": true
        }
    }
}
EOF

# Install coding standards tools
composer update

# Show installed packages
composer show --installed

# Check available coding standards
./vendor/bin/phpcs -i

# Configure PHPCS
./vendor/bin/phpcs --config-set colors 1
./vendor/bin/phpcs --config-set drupal_core_version ${TARGET_DRUPAL_CORE_VERSION}${TARGET_DRUPAL_CORE_VERSION}0

# Show final config
./vendor/bin/phpcs --config-show