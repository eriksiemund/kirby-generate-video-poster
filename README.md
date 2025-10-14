# Kirby Video Poster Generator

Kirby Video Poster Generator is a plugin for [Kirby CMS](https://getkirby.com) that uses Canvas API to get the initial or selected frame from a video file. It works directly in the Panel and is compatible with most Kirby websites.

## Install

You have to use composer to install the plugin into your project.

```console
composer require eriksiemund/generate-video-poster
```

## Setup

In the video file blueprint add a generatevideoposter field, browse to a video details page in panel and press the button.

*site/blueprints/files/video.yml*
```yaml
title: Default Page
fields:
  poster:
    label: My Poster
    type: files
    multiple: false
    width: 1/2
  my_generatevideoposter_button:
    type: generatevideoposter
```

## Use in Template or Snippet

*site/snippets/video.php*
```php
$poster = $media->toFile()->poster()
```

## Support and Questions

For the sake of reproducible bug reports, please include the following information in your bug reports:

- Kirby & Kirby Video Poster Generator version
- Browser environment (name, version, operating system)
- Global and section configuration (without any sensitive information)
- Steps to reproduce the bug (if no reproduction is provided)
- Screenshots or screen recordings if applicable

## Feedback

I value your feedback and ideas for improving Kirby Video Poster Generator. If you have any suggestions, please feel free to reach out to me.

## License

© 2025-PRESENT [Erik Siemund](https://github.com/eriksiemund)