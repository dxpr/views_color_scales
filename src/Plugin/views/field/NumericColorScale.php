<?php

namespace Drupal\views_color_scales\Plugin\views\field;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\views\Plugin\views\field\NumericField;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Render a field as a numeric value with Excel-style color scaling.
 *
 * This plugin can be used on any numeric field by specifying the 'real field'
 * in Views data definition or by selecting it as a field formatter.
 *
 * @ingroup views_field_handlers
 */
#[ViewsField("numeric_color_scale")]
class NumericColorScale extends NumericField {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a NumericColorScale object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var static */
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['color_scale'] = ['default' => FALSE];
    $options['color_scale_min'] = ['default' => '0'];
    $options['color_scale_max'] = ['default' => '100'];
    $options['color_scale_auto'] = ['default' => TRUE];
    $options['color_scale_min_color'] = ['default' => '#FFB3B3'];
    $options['color_scale_max_color'] = ['default' => '#B3FFB3'];
    $options['color_scale_min_label'] = ['default' => ''];
    $options['color_scale_mid_label'] = ['default' => ''];
    $options['color_scale_max_label'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['color_scale'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Color Scale'),
      '#description' => $this->t('Low values = red, high values = green.'),
      '#default_value' => $this->options['color_scale'],
    ];

    $form['color_scale_auto'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto-detect min/max values'),
      '#description' => $this->t('Automatically calculate minimum and maximum values from the dataset.'),
      '#default_value' => $this->options['color_scale_auto'],
      '#states' => [
        'visible' => [
          ':input[name="options[color_scale]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['color_scale_min'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum Value'),
      '#description' => $this->t('The minimum value for color scaling (will be red).'),
      '#default_value' => $this->options['color_scale_min'],
      '#step' => 0.01,
      '#states' => [
        'visible' => [
          ':input[name="options[color_scale]"]' => ['checked' => TRUE],
          ':input[name="options[color_scale_auto]"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['color_scale_max'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Value'),
      '#description' => $this->t('The maximum value for color scaling (will be green).'),
      '#default_value' => $this->options['color_scale_max'],
      '#step' => 0.01,
      '#states' => [
        'visible' => [
          ':input[name="options[color_scale]"]' => ['checked' => TRUE],
          ':input[name="options[color_scale_auto]"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['color_scale_colors'] = [
      '#type' => 'details',
      '#title' => $this->t('Color Configuration'),
      '#states' => [
        'visible' => [
          ':input[name="options[color_scale]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['color_scale_colors']['color_scale_min_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Minimum Value Color'),
      '#description' => $this->t('Color for the lowest values in the range.'),
      '#default_value' => $this->options['color_scale_min_color'],
    ];

    $form['color_scale_colors']['color_scale_max_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Maximum Value Color'),
      '#description' => $this->t('Color for the highest values in the range.'),
      '#default_value' => $this->options['color_scale_max_color'],
    ];

    $form['color_scale_labels'] = [
      '#type' => 'details',
      '#title' => $this->t('Scale Labels'),
      '#description' => $this->t('Semantic labels shown in the scale popover when hovering or focusing a value, for example "Negative", "Neutral" and "Positive".'),
      '#states' => [
        'visible' => [
          ':input[name="options[color_scale]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['color_scale_labels']['color_scale_min_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Minimum Value Label'),
      '#description' => $this->t('Label for the lowest end of the scale.'),
      '#default_value' => $this->options['color_scale_min_label'],
    ];

    $form['color_scale_labels']['color_scale_mid_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Middle Value Label'),
      '#description' => $this->t('Label for the middle of the scale.'),
      '#default_value' => $this->options['color_scale_mid_label'],
    ];

    $form['color_scale_labels']['color_scale_max_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Maximum Value Label'),
      '#description' => $this->t('Label for the highest end of the scale.'),
      '#default_value' => $this->options['color_scale_max_label'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = $this->getValue($values);
    $rendered = parent::render($values);

    // If color scale is not enabled or value is empty, return parent render
    if (!$this->options['color_scale'] || ($this->options['hide_empty'] && empty($value) && ($value !== 0 || $this->options['empty_zero']))) {
      return $rendered;
    }

    // Color interpolation range: auto-detected or configured.
    if ($this->options['color_scale_auto']) {
      $minMax = $this->getAutoMinMax();
      $colorMin = $minMax['min'];
      $colorMax = $minMax['max'];
    } else {
      $colorMin = (float) $this->options['color_scale_min'];
      $colorMax = (float) $this->options['color_scale_max'];
    }
    if ($colorMin == $colorMax) {
      $colorMax = $colorMin + 1;
    }

    $backgroundColor = $this->calculateColor((float) $value, $colorMin, $colorMax);
    $textColor = $this->getContrastColor($backgroundColor);

    // Gauge always uses the configured range so position matches detail pages.
    $gaugeMin = (float) $this->options['color_scale_min'];
    $gaugeMax = (float) $this->options['color_scale_max'];
    if ($gaugeMin == $gaugeMax) {
      $gaugeMax = $gaugeMin + 1;
    }
    $clamped = max($gaugeMin, min($gaugeMax, (float) $value));
    $position = round(($clamped - $gaugeMin) / ($gaugeMax - $gaugeMin), 4);

    $gauge = [
      '#theme' => 'analyze_gauge',
      '#caption' => '',
      '#range_min_label' => (string) $this->options['color_scale_min_label'],
      '#range_mid_label' => (string) $this->options['color_scale_mid_label'],
      '#range_max_label' => (string) $this->options['color_scale_max_label'],
      '#range_min' => $gaugeMin,
      '#value' => $position,
      '#display_value' => (string) $rendered,
      '#range_max' => $gaugeMax,
    ];

    $build = [
      '#type' => 'component',
      '#component' => 'views_color_scales:scale_popover',
      '#props' => [
        'value' => (float) $value,
        'bg_color' => $backgroundColor,
        'text_color' => $textColor,
        'popover_id' => Html::getUniqueId('vcs-popover'),
      ],
      '#slots' => [
        'display_value' => $rendered,
        'gauge' => $gauge,
      ],
    ];

    return $this->renderer->render($build);
  }

  /**
   * Calculate the background color based on value position in range.
   *
   * @param float $value
   *   The numeric value.
   * @param float $min
   *   The minimum value.
   * @param float $max
   *   The maximum value.
   *
   * @return string
   *   The calculated hex color.
   */
  private function calculateColor(float $value, float $min, float $max): string {
    // Clamp value to min/max range
    $value = max($min, min($max, $value));
    
    // Calculate position in range (0 to 1)
    $position = ($value - $min) / ($max - $min);
    
    // Get configured colors
    $min_color = $this->options['color_scale_min_color'];
    $max_color = $this->options['color_scale_max_color'];
    
    // Convert hex colors to RGB
    $min_rgb = $this->hexToRgb($min_color);
    $max_rgb = $this->hexToRgb($max_color);
    
    // Interpolate between colors
    $red = (int) ($min_rgb['r'] + ($max_rgb['r'] - $min_rgb['r']) * $position);
    $green = (int) ($min_rgb['g'] + ($max_rgb['g'] - $min_rgb['g']) * $position);
    $blue = (int) ($min_rgb['b'] + ($max_rgb['b'] - $min_rgb['b']) * $position);
    
    return sprintf('#%02X%02X%02X', $red, $green, $blue);
  }

  /**
   * Convert hex color to RGB array.
   *
   * @param string $hex
   *   Hex color string (e.g., '#FF0000').
   *
   * @return array
   *   Array with 'r', 'g', 'b' keys.
   */
  private function hexToRgb(string $hex): array {
    $hex = ltrim($hex, '#');
    
    return [
      'r' => hexdec(substr($hex, 0, 2)),
      'g' => hexdec(substr($hex, 2, 2)),
      'b' => hexdec(substr($hex, 4, 2)),
    ];
  }

  /**
   * Get contrasting text color for background.
   *
   * @param string $backgroundColor
   *   The background color in hex format.
   *
   * @return string
   *   Either 'white' or 'black' for best contrast.
   */
  private function getContrastColor(string $backgroundColor): string {
    // Remove # if present
    $color = ltrim($backgroundColor, '#');
    
    // Convert to RGB
    $r = hexdec(substr($color, 0, 2));
    $g = hexdec(substr($color, 2, 2));
    $b = hexdec(substr($color, 4, 2));
    
    // Calculate brightness using luminance formula
    $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    
    // Return black for bright backgrounds, white for dark
    return ($brightness > 128) ? 'black' : 'white';
  }

  /**
   * Auto-detect min and max values from the current result set.
   *
   * @return array
   *   Array with 'min' and 'max' keys.
   */
  private function getAutoMinMax(): array {
    $values = [];
    
    // Collect all values from the current result set
    foreach ($this->view->result as $row) {
      $value = $this->getValue($row);
      if (is_numeric($value)) {
        $values[] = (float) $value;
      }
    }
    
    if (empty($values)) {
      return ['min' => 0, 'max' => 1];
    }
    
    return [
      'min' => min($values),
      'max' => max($values),
    ];
  }

}