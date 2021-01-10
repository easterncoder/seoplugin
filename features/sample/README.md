# Sample Feature

This is a template for other features.

## File and Folder structure

### main.php

The main bootstrap file that loads the rest.

### config.php

The config file containing the `$name` and `$id` of the feature.

### README.md

This file you're reading right now.

### /assets

Contains all images, fonts, CSS and Javascript code that the feature needs.

### /includes

Contains all files that are to be included in order for the feature to work.

#### Auto-loading of classes in /includes

The plugin will attempt to autoload undefined classes from the `/includes` folder if the requested class is is namespaced as `SEOPlugin\Features\FeatureName\ClassName`. For example:

```php
new SEOPlugin\Features\Sample\MyClass;
```

will map to the following path:
```
features/sample/includes/class-myclass.php
```

which must contain the following:

```php
namespace SEOPlugin\Features\Sample;

class MyClass {}
```
