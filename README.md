# Views Color Scales

This module automatically enhances ALL numeric fields in Drupal Views with optional Excel-style color scaling. No duplicate fields, no complex setup - just enhanced formatting options.

## Features

- **Seamless Integration**: Automatically enhances all numeric fields in Views
- **Optional Color Scaling**: Enable/disable per field as needed
- **Configurable Colors**: Choose any colors for minimum and maximum values per field
- **Excel-style Gradients**: Smooth color transitions between your chosen colors
- **Auto-detection**: Automatically calculates min/max values from your dataset
- **Manual Range**: Set custom min/max values for consistent scaling
- **Smart Text Color**: Automatically uses black or white text for optimal contrast

## Installation

1. Place this module in `web/modules/contrib/views_color_scales/`
2. Enable the module: `drush en views_color_scales`
3. Clear cache: `drush cr`

## Usage

### Setting up Color Scaling

1. **Edit any View** with numeric data
2. **Configure any numeric field** (click on the field name)
3. **Find the new "Color Scale" options** among the standard formatting settings
4. **Enable Color Scale** and configure:
   - ✅ **Enable Color Scale**: Check this box
   - **Auto-detect min/max**: Recommended for most use cases
   - **Manual Range**: Set specific min/max values for consistent scaling
   - **Color Configuration**: Customize the minimum and maximum colors per field

### Perfect Use Cases

- **Sentiment Analysis Scores**: -1.0 (red) to +1.0 (green)
- **Performance Metrics**: 0% (red) to 100% (green)  
- **Survey Ratings**: 1 (red) to 5 (green)
- **Financial Data**: Losses (red) to profits (green)

### Example: Sentiment Analysis Dashboard

```
Field: Sentiment Score
Type: Numeric Color Scale
✅ Enable Color Scale
✅ Auto-detect min/max values
Min Value: -1.0 (if manual)
Max Value: 1.0 (if manual)
```

Result: Scores like -0.8 show as red, 0.0 as yellow, +0.8 as green.

## Configuration Options

### Color Scale Settings

- **Enable Color Scale**: Turn color scaling on/off
- **Auto-detect min/max**: Automatically find the range from your data
- **Manual Range**: Set specific minimum and maximum values
- **Standard Numeric Options**: All normal formatting options still available

### Color Scale Behavior

- **Soft Red (#FFB3B3)**: Minimum values (worst performance)
- **Light Pink (#E6CCE6)**: Middle values (average performance)  
- **Soft Green (#B3FFB3)**: Maximum values (best performance)
- **Text Color**: Automatically black or white for best readability

## Technical Details

- Extends Drupal's core `NumericField` plugin
- Calculates colors using linear interpolation
- Uses HSV color space for smooth transitions
- Adds minimal CSS for styling
- Compatible with all Views display formats

## Compatibility

- **Drupal**: 10.2+ and 11.x
- **PHP**: 8.1+
- **Dependencies**: Views (core module)

## Examples in Action

Perfect for these analysis modules:
- **Sentiment Analysis Results**: Color-code sentiment scores
- **Brand Voice Analysis**: Visualize alignment scores  
- **Performance Metrics**: Highlight best/worst performers
- **Survey Results**: Quick visual feedback on ratings

## Troubleshooting

### Colors not showing?
- Ensure "Enable Color Scale" is checked
- Verify the field contains numeric data
- Check that min/max values are different

### All same color?
- Your data might all be the same value
- Try "Auto-detect min/max" instead of manual range
- Check data source for variety in values

### Text hard to read?
- The module automatically chooses text color
- If issues persist, try custom CSS overrides

## Support

This module was created to enhance Views-based reporting and dashboards. It works excellently with sentiment analysis, brand voice analysis, and other numeric scoring systems.