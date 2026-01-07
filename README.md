# Must Use WordPress Plugins

This is a plugin that requires a set of other plugins that are (almost) always used in 26B projects.

## The plugins

The plugins are provided in this bundle, but they may also be used standalone, by downloading any of the files in the `plugins/` folder.

## Installing

Use composer to install, as it is required to extract the existing plugins into the base of the `mu-plugins` folder.

First configure the `extra` setting in `composer.json` so as to include the `wordpress-muplugin` composer package type as a target for the `mu-plugins/` folder.

```json
{
  "extra": {
    "installer-paths": {
      "mu-plugins/{$name}/": [
        "type:wordpress-muplugin"
      ]
    }
  },
}
```

Then require the dependency.

```bash
composer required 26b/wp-must-use
```
