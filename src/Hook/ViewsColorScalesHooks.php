<?php

namespace Drupal\views_color_scales\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
/**
 * Hook implementations for views_color_scales.
 */
class ViewsColorScalesHooks
{
    use StringTranslationTrait;
    /**
     * @file
     * Views Color Scales module for Excel-style color scaling in Views.
     */
    /**
     * Implements hook_help().
     */
    #[Hook('help')]
    public function help($route_name, $route_match)
    {
        switch ($route_name) {
            case 'help.page.views_color_scales':
                return '<p>' . $this->t('The Views Color Scales module automatically enhances ALL numeric fields in Views with optional Excel-style color scaling. When editing any numeric field, you will find new "Color Scale" options alongside the standard formatting settings like precision and decimal points.') . '</p>';
        }
    }
    /**
     * Implements hook_views_data_alter().
     */
    #[Hook('views_data_alter')]
    public function viewsDataAlter(array &$data)
    {
        // Replace all numeric field handlers with our enhanced version.
        foreach ($data as $table_name => &$table_data) {
            if (isset($table_data['table'])) {
                foreach ($table_data as $field_name => &$field_data) {
                    // Skip table definition.
                    if ($field_name === 'table') {
                        continue;
                    }
                    // Check if this is a numeric field.
                    if (isset($field_data['field']['id']) && $field_data['field']['id'] === 'numeric') {
                        // Replace with our enhanced numeric field handler.
                        $field_data['field']['id'] = 'numeric_color_scale';
                    }
                }
            }
        }
    }
}
