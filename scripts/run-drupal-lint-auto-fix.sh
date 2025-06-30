#!/bin/bash

source scripts/prepare-drupal-lint.sh

# Auto-fix Drupal coding standards
echo "Auto-fixing Drupal coding standards..."
./vendor/bin/phpcbf --standard=Drupal \
  --extensions=php,module,inc,install,test,profile,theme,info,txt,md,yml \
  --ignore="node_modules,vendor,.github" \
  .

# Auto-fix Drupal best practices
echo "Auto-fixing Drupal best practices..."
./vendor/bin/phpcbf --standard=DrupalPractice \
  --extensions=php,module,inc,install,test,profile,theme,info,txt,md,yml \
  --ignore="node_modules,vendor,.github" \
  .