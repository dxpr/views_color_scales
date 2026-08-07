<?php

/**
 * @file
 * Hooks provided by the Views Color Scales module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the popover content for a color-scaled numeric field.
 *
 * Modules implement this hook to inject any render array into the popover
 * that appears when a user hovers or focuses a color-scaled value. The
 * popover is a generic container: gauges, images, tables, or any other
 * renderable content can be placed inside.
 *
 * @param array $content
 *   A render array for the popover body, initially empty. Replace or extend
 *   it with your own content.
 * @param array $context
 *   Contextual information about the value being rendered:
 *   - value: (float) The raw numeric value.
 *   - display_value: (\Drupal\Component\Render\MarkupInterface|string) The
 *     formatted value shown in the table cell.
 *   - min: (float) The configured minimum of the gauge range.
 *   - max: (float) The configured maximum of the gauge range.
 *   - position: (float) Normalised position within [min, max], from 0 to 1.
 *   - field_options: (array) All options from the Views field handler.
 *   - view: (\Drupal\views\ViewExecutable) The executing view.
 *   - row: (\Drupal\views\ResultRow) The current result row.
 *
 * @see \Drupal\views_color_scales\Plugin\views\field\NumericColorScale::render()
 */
function hook_views_color_scale_popover_alter(array &$content, array $context): void {
  // Only act on a specific view's base table.
  if ($context['view']->storage->get('base_table') !== 'my_module_results') {
    return;
  }

  $content = [
    '#theme' => 'my_module_gauge',
    '#value' => $context['position'],
    '#display_value' => $context['display_value'],
    '#range_min' => $context['min'],
    '#range_max' => $context['max'],
  ];
}

/**
 * @} End of "addtogroup hooks".
 */
